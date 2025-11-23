<?php
// generate-character.php
// Backend endpoint that calls OpenAI to create a character profile
// and a list of expansive roleplay avenues.

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$rawBody = file_get_contents('php://input');
$input = json_decode($rawBody, true);
if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body']);
    exit;
}

// Get your OpenAI API key from an environment variable or hard-code it for local testing.
// Recommended: set OPENAI_API_KEY in your environment.

 $apiKey = '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';

// QUICK LOCAL TEST OPTION (uncomment and put your real key ONLY for local use, never public):
// $apiKey = 'sk-proj-REPLACE_ME_WITH_YOUR_KEY';

if (!$apiKey) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Missing OpenAI API key. Set OPENAI_API_KEY or hard-code it for local testing.'
    ]);
    exit;
}

// Extract and sanitize inputs
$name      = isset($input['name'])      ? trim($input['name'])      : '';
$age       = isset($input['age'])       ? trim($input['age'])       : '';
$ethnicity = isset($input['ethnicity']) ? trim($input['ethnicity']) : '';
$hometown  = isset($input['hometown'])  ? trim($input['hometown'])  : '';
$livingIn  = isset($input['livingIn'])  ? trim($input['livingIn'])  : '';
$height    = isset($input['height'])    ? trim($input['height'])    : '';
$weight    = isset($input['weight'])    ? trim($input['weight'])    : '';
$wealth    = isset($input['wealth'])    ? trim($input['wealth'])    : '';
$vibe      = isset($input['vibe'])      ? trim($input['vibe'])      : '';

$nameText = ($name !== '')
    ? $name
    : 'No name provided; choose a natural, realistic full name that fits the character.';

$prompt = <<<PROMPT
You are helping to create a fictional character concept.
The default setting is contemporary and realistic unless the user's notes clearly suggest something else.
Avoid referencing any external works or prior conversations.

Character information provided by the user (some fields may be blank):
Name: {$nameText}
Age: {$age}
Ethnicity: {$ethnicity}
Place of origin: {$hometown}
Current place of living: {$livingIn}
Height: {$height}
Build / weight: {$weight}
Financial situation: {$wealth}
Additional notes from the user: {$vibe}

Rules about missing information:
- If any field is missing or vague (age, ethnicity, place of origin, current place of living, height, build, financial situation), you may invent plausible details that fit the character and use them consistently.
- If an age is provided, infer a reasonable date of birth that matches that age (you may pick a specific day and month). If age is missing, choose a fitting age and a matching date of birth.
- You will return the inferred or completed information in a structured "profile" object.

Your tasks:
1. If the name is missing or unclear, choose a natural full name that matches the character.
2. Do NOT write one single continuous backstory.
3. Instead, create 10–20 expansive, alternative roleplay avenues for this character.
4. Each avenue should be a short multi-sentence scenario (about 2–5 sentences) that:
   - Starts with a brief sense of their early life or upbringing.
   - If the place of origin differs from the current place of living, briefly explain the move and why it happened.
   - Describes their current life situation, habits, or circumstances.
   - Ends with at least one clear hook or direction for future roleplay (a conflict, goal, unresolved relationship, secret, etc.).
5. Treat each avenue as a separate possible version or direction for the character, not as different chapters of one fixed story.
6. Keep the writing grounded and coherent, but flexible enough for the player to build on.
7. Output ONLY strict JSON, with no markdown, no bullet characters, no numbering, and no extra commentary.

Return your response in this exact JSON shape:
{
  "name": "Final full name",
  "profile": {
    "age": "Age as a number or short phrase",
    "ethnicity": "Ethnicity",
    "place_of_origin": "Place of origin",
    "current_living": "Current place of living",
    "height": "Height or description",
    "build": "Build / weight description",
    "financial_situation": "Financial situation",
    "date_of_birth": "Inferred date of birth"
  },
  "avenues": ["Avenue 1 text", "Avenue 2 text", "Avenue 3 text"]
}
PROMPT;

// Call OpenAI Responses API
$ch = curl_init('https://api.openai.com/v1/responses');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

$body = [
    'model' => 'gpt-4.1-mini',
    'input' => $prompt,
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch);
if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

$data = json_decode($response, true);
if ($data === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON from OpenAI', 'raw' => $response]);
    exit;
}

// Extract the text from the Responses API structure
$text = null;
if (isset($data['output'][0]['content'][0]['text'])) {
    $text = $data['output'][0]['content'][0]['text'];
} elseif (isset($data['output_text'])) {
    $text = $data['output_text'];
}

if ($text === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Unexpected OpenAI response structure', 'raw' => $data]);
    exit;
}

// Strip possible ```json fences
$trimmed = trim($text);
$trimmed = preg_replace('/^```[a-zA-Z0-9]*\s*/', '', $trimmed);
$trimmed = preg_replace('/\s*```$/', '', $trimmed);

// Try to parse as JSON
$parsed = json_decode($trimmed, true);

// Fallback if JSON fails or structure is incomplete
if (!is_array($parsed) || !isset($parsed['avenues'])) {
    $lines = preg_split('/\r\n|\r|\n/', $trimmed);
    $ideas = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $line = preg_replace('/^[-*•]\s*/', '', $line);
        if ($line !== '') {
            $ideas[] = $line;
        }
    }
    if (empty($ideas)) {
        $ideas[] = $trimmed;
    }

    $parsed = [
        'name'    => ($name !== '' ? $name : 'Unnamed character'),
        'profile' => [
            'age'                 => $age,
            'ethnicity'           => $ethnicity,
            'place_of_origin'     => $hometown,
            'current_living'      => $livingIn,
            'height'              => $height,
            'build'               => $weight,
            'financial_situation' => $wealth,
            'date_of_birth'       => ''
        ],
        'avenues' => $ideas,
    ];
}

// Make sure profile sub-object exists
$profile = isset($parsed['profile']) && is_array($parsed['profile']) ? $parsed['profile'] : [];
$profile = array_merge([
    'age'                 => '',
    'ethnicity'           => '',
    'place_of_origin'     => '',
    'current_living'      => '',
    'height'              => '',
    'build'               => '',
    'financial_situation' => '',
    'date_of_birth'       => '',
], $profile);

// Final JSON back to frontend
echo json_encode([
    'name'    => $parsed['name'],
    'profile' => $profile,
    'avenues' => $parsed['avenues'],
]);

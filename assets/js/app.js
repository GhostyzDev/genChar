// assets/js/app.js
// Front-end logic for Character & Roleplay Avenue Generator (with AI details + spinner)

// Helper to safely get an element by ID
function $(id) {
  return document.getElementById(id) || null;
}

// Helper to safely get .value.trim() from an element
function fieldValue(el) {
  return el && typeof el.value === "string" ? el.value.trim() : "";
}

// Form fields
const nameInput = $('name');
const aiNameCheckbox = $('aiName');
const ageInput = $('age');
const ethnicityInput = $('ethnicity');
const dobInput = $('dob'); // may be absent; handled defensively
const hometownInput = $('hometown');
const livingInInput = $('livingIn');
const heightInput = $('height');
const weightInput = $('weight');
const wealthInput = $('wealth');
const vibeInput = $('vibe');

// Buttons / status
const randomizeNameBtn = $('randomizeNameBtn');
const generateBtn = $('generateBtn');
const statusEl = $('status');

// Result elements
const resultCard = $('result');
const resultNameEl = $('resultName');
const resultMetaEl = $('resultMeta');
const resultBodyEl = $('resultBackstory'); // displays avenues
const aiDetailsEl = $('aiDetails');
const placeholderEl = $('placeholder');
const loadingSpinnerEl = $('loadingSpinner');

// Copy controls
const copyAllBtn = $('copyAllBtn');
const copyStatusEl = $('copyStatus');

// Neutral example name pool
const firstNames = [
  "Alex", "Taylor", "Morgan", "Jamie", "Casey", "Jordan",
  "Avery", "Logan", "Quinn", "Elliot", "Riley", "Harper",
  "Rowan", "Cameron", "Blake", "Emerson", "Finley", "Hayden",
  "Sage", "Dakota", "Phoenix", "Remy", "Lane", "Eden",
  "Arden", "Blair", "Charlie", "Micah", "River", "Alexis",
  "Kendall", "Devon", "Reagan", "Tatum", "Ainsley", "Bailey",
  "Shiloh", "Jesse", "Marley", "Sydney", "Kirby", "Hollis",
  "Sutton", "Oakley", "Indigo", "Onyx", "Sasha", "Ellis",
  "Reign", "Briar", "London", "Nico", "Haven", "Rain",
  "Tristan", "Marlow", "Zephyr", "Cove", "Gray", "Sol",
  "Harlow", "Shay", "Jules", "Lior", "Dorian", "Ari",
  "Kieran", "Mika", "Jaden", "Callen", "Noel", "Rowe",
  "Ariel", "Skyler", "Peyton", "Emery", "Rio", "Rory",
  "Bellamy", "Cleo", "Kit", "Lux", "Monroe", "Nova",
  "Piper", "Rene", "Sloane", "Tori", "Winter", "Zuri",
  "Aspen", "Dallas", "Justice", "Robin"
];

const lastNames = [
  "Anderson", "Bennett", "Brooks", "Carter", "Clark", "Coleman",
  "Collins", "Cook", "Cooper", "Cox", "Davis", "Diaz",
  "Edwards", "Evans", "Fisher", "Ford", "Foster", "Garcia",
  "Gibson", "Gomez", "Gonzalez", "Gordon", "Graham", "Gray",
  "Green", "Griffin", "Hall", "Hamilton", "Harris", "Harrison",
  "Hayes", "Henderson", "Hernandez", "Hill", "Hughes", "Jackson",
  "James", "Jenkins", "Johnson", "Jones", "Kelly", "King",
  "Lee", "Lewis", "Long", "Lopez", "Marshall", "Martin",
  "Martinez", "Miller", "Mitchell", "Moore", "Morales", "Morgan",
  "Morris", "Murphy", "Myers", "Nelson", "Nguyen", "Ortiz",
  "Parker", "Patterson", "Perez", "Perry", "Peterson", "Phillips",
  "Powell", "Price", "Ramos", "Reed", "Reyes", "Richardson",
  "Rivera", "Roberts", "Robinson", "Rodriguez", "Rogers", "Ross",
  "Russell", "Sanchez", "Sanders", "Scott", "Simmons", "Smith",
  "Stewart", "Taylor", "Thomas", "Thompson", "Torres", "Turner",
  "Walker", "Ward", "Watson", "White", "Williams", "Wilson",
  "Wood", "Wright", "Young", "Zimmerman"
];

function getRandom(arr) {
  return arr[Math.floor(Math.random() * arr.length)];
}

function generateRandomName() {
  return `${getRandom(firstNames)} ${getRandom(lastNames)}`;
}

// Warn if critical elements missing
(function sanityCheck() {
  const critical = {
    nameInput,
    generateBtn,
    resultBodyEl
  };
  Object.entries(critical).forEach(([key, el]) => {
    if (!el) {
      console.warn(`[CharacterApp] Missing element for: ${key}. Check IDs in index.php.`);
    }
  });
})();

// Random name button
if (randomizeNameBtn && nameInput) {
  randomizeNameBtn.addEventListener('click', () => {
    nameInput.value = generateRandomName();
  });
}

// AI name toggle
if (aiNameCheckbox && nameInput) {
  aiNameCheckbox.addEventListener('change', () => {
    if (aiNameCheckbox.checked && !fieldValue(nameInput)) {
      nameInput.value = generateRandomName();
    }
  });
}

function setLoading(isLoading, message = "") {
  if (generateBtn) generateBtn.disabled = isLoading;
  if (randomizeNameBtn) randomizeNameBtn.disabled = isLoading;
  if (statusEl) {
    statusEl.textContent = message;
    statusEl.classList.remove('error');
  }
  if (loadingSpinnerEl) {
    if (isLoading) {
      loadingSpinnerEl.classList.remove('result-hidden');
    } else {
      loadingSpinnerEl.classList.add('result-hidden');
    }
  }
}

function setError(message) {
  if (statusEl) {
    statusEl.textContent = message;
    statusEl.classList.add('error');
  } else {
    console.error("[CharacterApp] Error:", message);
  }
}

function buildMetaLine(label, value) {
  if (!value) return "";
  return `<span><strong>${label}:</strong> ${value}</span>`;
}

function renderAiDetails(profile) {
  if (!aiDetailsEl || !profile) return;

  const items = [];

  if (profile.age)                 items.push(`<li><strong>Age:</strong> ${profile.age}</li>`);
  if (profile.date_of_birth)      items.push(`<li><strong>Date of birth:</strong> ${profile.date_of_birth}</li>`);
  if (profile.ethnicity)          items.push(`<li><strong>Ethnicity:</strong> ${profile.ethnicity}</li>`);
  if (profile.place_of_origin)    items.push(`<li><strong>Place of origin:</strong> ${profile.place_of_origin}</li>`);
  if (profile.current_living)     items.push(`<li><strong>Current location:</strong> ${profile.current_living}</li>`);
  if (profile.height)             items.push(`<li><strong>Height:</strong> ${profile.height}</li>`);
  if (profile.build)              items.push(`<li><strong>Build:</strong> ${profile.build}</li>`);
  if (profile.financial_situation)items.push(`<li><strong>Financial situation:</strong> ${profile.financial_situation}</li>`);

  if (items.length === 0) {
    aiDetailsEl.innerHTML = "";
    return;
  }

  aiDetailsEl.innerHTML = `
    <h3>AI-filled details</h3>
    <ul>
      ${items.join("")}
    </ul>
  `;
}

async function handleGenerate() {
  setLoading(true, "Generating character ideas with AI...");
  if (copyStatusEl) copyStatusEl.textContent = "";
  if (aiDetailsEl) aiDetailsEl.innerHTML = "";
  if (resultBodyEl) resultBodyEl.textContent = "";

  try {
    let name = fieldValue(nameInput);
    if ((aiNameCheckbox && aiNameCheckbox.checked) || !name) {
      name = "";
    }

    const payload = {
      name,
      age: fieldValue(ageInput),
      ethnicity: fieldValue(ethnicityInput),
      dob: dobInput ? dobInput.value : "",
      hometown: fieldValue(hometownInput),
      livingIn: fieldValue(livingInInput),
      height: fieldValue(heightInput),
      weight: fieldValue(weightInput),
      wealth: fieldValue(wealthInput),
      vibe: fieldValue(vibeInput)
    };

    const res = await fetch('generate-character.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (!res.ok) {
      const errText = await res.text();
      console.error("[CharacterApp] Server error payload:", errText);
      throw new Error("Server error: " + res.status);
    }

    const data = await res.json();
    console.log("[CharacterApp] API response:", data);

    const finalName = data.name || name || "Unnamed character";
    const avenues = Array.isArray(data.avenues) ? data.avenues : [];
    const profile = data.profile || {};

    if (nameInput) {
      nameInput.value = finalName;
    }

    // Build meta section from profile (short)
    const metaPieces = [];
    if (profile.age) metaPieces.push(buildMetaLine("Age", profile.age));
    if (profile.place_of_origin) metaPieces.push(buildMetaLine("From", profile.place_of_origin));
    if (profile.current_living) metaPieces.push(buildMetaLine("Lives in", profile.current_living));

    if (resultNameEl) resultNameEl.textContent = finalName;
    if (resultMetaEl) resultMetaEl.innerHTML = metaPieces.join("");

    // AI-filled details block
    renderAiDetails(profile);

    // Render avenues as paragraphs
    if (resultBodyEl) {
      if (avenues.length > 0) {
        resultBodyEl.innerHTML = avenues
          .map(a => `<p>• ${a}</p>`)
          .join("");
      } else {
        resultBodyEl.textContent = "No roleplay ideas were generated.";
      }
    }

    if (resultCard) resultCard.classList.remove('result-hidden');
    if (placeholderEl) placeholderEl.style.display = 'none';

    setLoading(false, "Done. You can regenerate or adjust details anytime.");
  } catch (err) {
    console.error("[CharacterApp] Exception in handleGenerate:", err);
    setError("Something went wrong while generating the character ideas. Check your API key or server logs.");
    setLoading(false);
  }
}

if (generateBtn) {
  generateBtn.addEventListener('click', handleGenerate);
}

// Copy name + avenues + AI details as plain text
if (copyAllBtn) {
  copyAllBtn.addEventListener('click', async () => {
    if (copyStatusEl) copyStatusEl.textContent = "";

    const name = resultNameEl ? resultNameEl.textContent.trim() : "";
    const ideasHtml = resultBodyEl ? resultBodyEl.innerHTML : "";
    const detailsHtml = aiDetailsEl ? aiDetailsEl.innerHTML : "";

    if (!name && !ideasHtml && !detailsHtml) {
      if (copyStatusEl) copyStatusEl.textContent = "Nothing to copy yet.";
      return;
    }

    const temp = document.createElement("div");
    temp.innerHTML = ideasHtml;
    const textIdeas = (temp.textContent || temp.innerText || "").trim();

    const temp2 = document.createElement("div");
    temp2.innerHTML = detailsHtml;
    const textDetails = (temp2.textContent || temp2.innerText || "").trim();

    const parts = [];
    if (name) parts.push(name);
    if (textDetails) parts.push("\nAI-filled details:\n" + textDetails);
    if (textIdeas) parts.push("\nRoleplay avenues:\n" + textIdeas);

    const textBlock = parts.join("\n\n");

    try {
      await navigator.clipboard.writeText(textBlock);
      if (copyStatusEl) copyStatusEl.textContent = "Copied to clipboard.";
    } catch (err) {
      console.error("[CharacterApp] Clipboard error:", err);
      if (copyStatusEl) copyStatusEl.textContent = "Could not copy automatically. You can select and copy manually.";
    }
  });
}

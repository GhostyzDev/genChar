<?php
// index.php - Main page for Character & Backstory Generator (clean neutral version)
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Character & Backstory Generator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body>
  <div class="app-root">
    <header class="app-header">
      <div class="title-block">
        <h1>Character & Backstory Generator</h1>
        <p class="subtitle">
          Enter a few details and let the AI create a complete character profile and narrative backstory.
        </p>
      </div>
    </header>

    <main class="app-main">
      <section class="column column-left">
        <h2 class="section-title">Character details</h2>
        <form id="character-form">
          <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" placeholder="Leave blank to let the AI decide" />
            <label class="inline-checkbox">
              <input type="checkbox" id="aiName" />
              <span>Let the AI choose a name</span>
            </label>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="age">Age</label>
              <input id="age" name="age" type="number" min="1" max="120" placeholder="e.g. 28" />
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="ethnicity">Ethnicity (optional)</label>
              <input id="ethnicity" name="ethnicity" type="text" placeholder="White, African American, Hispanic, etc." />
            </div>
            <div class="field">
              <label for="wealth">Financial situation</label>
              <select id="wealth" name="wealth">
                <option value="">Prefer not to say</option>
                <option value="Struggling financially">Struggling financially</option>
                <option value="Working class">Working class</option>
                <option value="Comfortable / stable">Comfortable / stable</option>
                <option value="Well-off">Well-off</option>
                <option value="Wealthy">Wealthy</option>
              </select>
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="hometown">Place of origin</label>
              <input id="hometown" name="hometown" type="text" placeholder="e.g. Liberty City, Sandy Shores, etc" />
            </div>
            <div class="field">
              <label for="livingIn">Currently living in</label>
              <input id="livingIn" name="livingIn" type="text" placeholder="e.g. Los Santos, Paleto Bay" />
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="height">Height (optional)</label>
              <input id="height" name="height" type="text" placeholder="e.g. 180 cm / 5'11&quot;" />
            </div>
            <div class="field">
              <label for="weight">Build / weight (optional)</label>
              <input id="weight" name="weight" type="text" placeholder="e.g. Average build, athletic, etc." />
            </div>
          </div>

          <div class="field">
            <label for="vibe">
              Extra notes for the AI
              <span class="hint">Optional. For example: "introverted but observant", "optimistic with a complicated past".</span>
            </label>
            <textarea id="vibe" name="vibe" placeholder="Describe personality, goals, themes, tone, or anything you'd like the story to lean toward."></textarea>
          </div>

          <div class="actions">
            <button type="button" class="btn secondary" id="randomizeNameBtn">Random name</button>
            <button type="button" class="btn primary" id="generateBtn">Generate character</button>
          </div>
          <div id="status" class="status"></div>
        </form>

        <section class="info-card">
          <h3>Usage tips</h3>
          <ul>
            <li>You can leave most fields blank and let the AI fill in the gaps.</li>
            <li>Use the notes field to steer genre, mood, or role (for example, "mentor figure" or "supports drama").</li>
            <li>You can regenerate multiple times and pick the version you like best.</li>
          </ul>
        </section>
      </section>

      <section class="column column-right">
        <h2 class="section-title">Result</h2>
        <div id="loadingSpinner" class="loading-spinner result-hidden">
  <div class="spinner-circle"></div>
  <span>Generating character…</span>
</div>

        <article id="result" class="result-card result-hidden">
  <header class="result-header">
    <div class="result-title">
      <h2 id="resultName"></h2>
      <span class="tag">Generated profile</span>
    </div>
    <div id="resultMeta" class="result-meta"></div>
  </header>

  <section id="aiDetails" class="ai-details"></section>

  <section id="resultBackstory" class="result-body"></section>
</article>


        <div id="placeholder" class="placeholder">
          <p>Generate a character to see the profile and backstory here.</p>
        </div>

        <div class="copy-row">
          <button type="button" class="btn ghost" id="copyAllBtn">Copy name & backstory</button>
          <span id="copyStatus" class="copy-status"></span>
        </div>
      </section>
    </main>

    <footer class="app-footer">
      <span>Character & Backstory Generator</span>
      <span>Front-end: HTML, CSS, JS · Backend: PHP + OpenAI API</span>
    </footer>
  </div>

<script src="assets/js/app.js?v=5"></script>


</body>
</html>

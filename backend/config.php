<?php
// config.php

// --- MySQL ---
define('DB_HOST', 'mysql.meusimulador.com');
define('DB_USER', 'meusimulador');
define('DB_PASSWORD', '@OrizzontteBI2025');
define('DB_NAME', 'meusimulador');

// --- BigQuery ---
define('BQ_CREDENTIALS_PATH', __DIR__ . '/orizzonttebi-2743f6195331.json');
define('BQ_PROJECT_ID', 'orizzonttebi');
define('BQ_DATASET_ID', 'form_data');
define('BQ_TABLE_ID', 'contacts');

// --- reCAPTCHA ---
define('RECAPTCHA_SECRET_KEY', '6Le2FWsrAAAAAK-CV3BsGLlgNZthaRya3zxPwalG');

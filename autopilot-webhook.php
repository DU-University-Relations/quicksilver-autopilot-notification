<?php
/**
 * @file
 * Sends Autopilot VRT data to the Drupal webhook endpoint.
 */

// Get webhook configuration from Pantheon secrets.
$webhook_base_url = pantheon_get_secret('autopilot_webhook_url');
$webhook_token = pantheon_get_secret('autopilot_webook_token');

if (empty($webhook_base_url)) {
  echo "Error: Missing autopilot_webhook_url secret. Configure with:\n";
  echo "terminus secret:set SITE.ENV autopilot_webhook_url https://your-site.pantheonsite.io/pantheon-autopilot/webhook/vrt-status\n";
  exit(1);
}

if (empty($webhook_token)) {
  echo "Error: Missing webhook_token secret. Configure with:\n";
  echo "terminus secret:set SITE.ENV webhook_token YOUR_SECURE_TOKEN\n";
  exit(1);
}

// Validate required POST data.
if (empty($_POST['vrt_status']) || empty($_POST['vrt_result_url'])) {
  echo "Error: Missing required POST data (vrt_status, vrt_result_url).\n";
  exit(1);
}

// Get Pantheon environment information.
$site_id = $_ENV['PANTHEON_SITE'] ?? 'unknown';
$site_name = $_ENV['PANTHEON_SITE_NAME'] ?? 'unknown';
$environment = $_ENV['PANTHEON_ENVIRONMENT'] ?? 'unknown';

// Prepare webhook payload.
$payload = [
  'status' => $_POST['vrt_status'],
  'vrt_result_url' => $_POST['vrt_result_url'],
  'site_id' => $site_id,
  'site_name' => $site_name,
  'environment' => $environment,
  'updates_info' => !empty($_POST['updates_info']) ? json_decode($_POST['updates_info'], TRUE) : [],
];

// Build webhook URL with token.
$webhook_url = $webhook_base_url . '?token=' . urlencode($webhook_token);

// Send webhook request.
$context = stream_context_create([
  'http' => [
    'method' => 'POST',
    'header' => 'Content-Type: application/json',
    'content' => json_encode($payload),
    'ignore_errors' => TRUE,
  ],
]);

$response = file_get_contents($webhook_url, FALSE, $context);
$status_code = $http_response_header[0] ?? '';

if (str_contains($status_code, '200')) {
  echo "Webhook notification sent successfully to Drupal\n";
  echo "VRT Status: {$payload['status']}\n";
  echo "Environment: {$site_name}.{$environment}\n";
}
else {
  echo "Failed to send webhook notification: $status_code\n";
  echo "Response: $response\n";
  exit(1);
}

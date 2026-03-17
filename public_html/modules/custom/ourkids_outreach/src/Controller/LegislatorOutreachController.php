<?php

namespace Drupal\ourkids_outreach\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Renders the legislator outreach admin page.
 */
class LegislatorOutreachController extends ControllerBase {

  const SITE_URL   = 'https://ourkidsourschoolsvt.org/';
  const REF_PARAM  = 'ref';
  const FROM_EMAIL = 'sean@montagues.net';
  const FROM_NAME  = 'Sean Montague';
  const SUBJECT    = "Vermont's Independent Schools and Rural Education";

  /**
   * Main admin page.
   */
  public function page(): array {
    $legislators = $this->loadLegislators();
    if (!$legislators) {
      return ['#markup' => $this->t('Could not load legislators data.')];
    }

    return [
      '#theme' => 'ourkids_outreach_page',
      '#representatives' => $this->buildList($legislators['representatives'] ?? [], 'h'),
      '#senators'        => $this->buildList($legislators['senators'] ?? [], 's'),
      '#attached' => [
        'library' => ['ourkids_outreach/outreach'],
        'drupalSettings' => [
          'ourkidsOutreach' => [
            'previewUrl' => '/admin/ourkids/legislator-outreach/preview',
            'sendUrl'    => '/admin/ourkids/legislator-outreach/send',
          ],
        ],
      ],
    ];
  }

  /**
   * Returns a JSON preview of the email for a given ref.
   */
  public function preview(Request $request): JsonResponse {
    $ref = $request->query->get('ref', '');
    $leg = $this->findLegislator($ref);

    if (!$leg) {
      return new JsonResponse(['error' => 'Legislator not found.'], 404);
    }

    return new JsonResponse([
      'name'    => $leg['name'],
      'email'   => $leg['email'],
      'subject' => self::SUBJECT,
      'body'    => $this->buildHtmlBody($leg),
    ]);
  }

  /**
   * Sends the email for a given ref via Drupal Mail API.
   */
  public function send(Request $request): JsonResponse {
    if ($request->getMethod() !== 'POST') {
      return new JsonResponse(['error' => 'POST required.'], 405);
    }

    $ref = $request->request->get('ref', '');
    $leg = $this->findLegislator($ref);

    if (!$leg) {
      return new JsonResponse(['error' => 'Legislator not found.'], 404);
    }

    $mailManager = \Drupal::service('plugin.manager.mail');
    $params = [
      'to'      => $leg['email'],
      'subject' => self::SUBJECT,
      'body'    => $this->buildHtmlBody($leg),
      'headers' => [
        'Content-Type' => 'text/html; charset=UTF-8',
        'From'         => self::FROM_NAME . ' <' . self::FROM_EMAIL . '>',
        'Reply-To'     => self::FROM_EMAIL,
      ],
    ];

    $result = $mailManager->mail(
      'ourkids_outreach',
      'legislator_outreach',
      $leg['email'],
      'en',
      $params,
      self::FROM_EMAIL,
      TRUE
    );

    if ($result['result']) {
      return new JsonResponse(['success' => TRUE, 'ref' => $ref]);
    }

    return new JsonResponse(['error' => 'Mail send failed.'], 500);
  }

  /**
   * Builds the HTML email body for a legislator.
   */
  protected function buildHtmlBody(array $leg): string {
    $tracked_url = self::SITE_URL . '?' . self::REF_PARAM . '=' . urlencode($leg['ref']);
    $salutation  = preg_replace('/^(Representative|Senator)\s+/i', '', $leg['name']);
    $from_email  = self::FROM_EMAIL;

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body style="font-family: Georgia, serif; font-size: 16px; line-height: 1.7; color: #1a1a1a; max-width: 600px; margin: 0 auto; padding: 2rem;">

<p>Dear {$salutation},</p>

<p>My name is Sean Montague. I am a constituent from Burke Hollow, Vermont, and a parent of two students — one attending St. Johnsbury Academy and one at Riverside School.</p>

<p>I am writing to share my concerns about the impact of current education legislation on rural Vermont communities that depend on independent schools for public secondary education. For towns like mine, tuitioning is not a policy preference — it is how public education has always been delivered.</p>

<p>Act 73 has already reduced the number of independent schools eligible to receive public tuition dollars from 46 to 18. The legislation currently moving through Montpelier would compound that harm, and in doing so would directly affect families who have no alternative.</p>

<p>I would welcome the opportunity to speak with you directly about how these proposals affect families in the Northeast Kingdom and across Vermont.</p>

<p>You can learn more about our coalition at <a href="{$tracked_url}" style="color: #8b1a1a;">OurKidsOurSchoolsVT.org</a>.</p>

<p>Thank you for your service to Vermont.</p>

<p>
  Sean Montague<br>
  Burke Hollow, VT<br>
  <a href="mailto:{$from_email}" style="color: #8b1a1a;">{$from_email}</a>
</p>

</body>
</html>
HTML;
  }

  /**
   * Loads and returns the legislators JSON data.
   */
  protected function loadLegislators(): ?array {
    $path = \Drupal::service('extension.list.theme')
      ->getPath('surface') . '/data/vt-legislators.json';

    if (!file_exists($path)) {
      return NULL;
    }

    $data = json_decode(file_get_contents($path), TRUE);
    return $data ?: NULL;
  }

  /**
   * Finds a legislator by ref token across both chambers.
   */
  protected function findLegislator(string $ref): ?array {
    $data = $this->loadLegislators();
    if (!$data) return NULL;

    $all = array_merge(
      $this->buildList($data['representatives'] ?? [], 'h'),
      $this->buildList($data['senators'] ?? [], 's')
    );

    foreach ($all as $leg) {
      if ($leg['ref'] === $ref) return $leg;
    }

    return NULL;
  }

  /**
   * Builds a processed list of legislators with ref tokens.
   */
  protected function buildList(array $legislators, string $chamber): array {
    $list = [];

    foreach ($legislators as $leg) {
      $name     = $leg['name']     ?? '';
      $email    = $leg['email']    ?? '';
      $district = $leg['district'] ?? '';

      if (!$email) continue;

      $list[] = [
        'name'     => $name,
        'email'    => $email,
        'district' => $district,
        'ref'      => $this->buildRef($name, $chamber, $district),
        'chamber'  => $chamber,
      ];
    }

    usort($list, fn($a, $b) => strcmp($this->lastName($a['name']), $this->lastName($b['name'])));

    return $list;
  }

  /**
   * Builds a short opaque ref token.
   * Format: {initials}-{chamber}-{district_initials}
   */
  protected function buildRef(string $name, string $chamber, string $district): string {
    $clean    = preg_replace('/^(Representative|Senator)\s+/i', '', $name);
    $words    = preg_split('/\s+/', trim($clean));
    $initials = strtolower(($words[0][0] ?? '') . ($words[count($words) - 1][0] ?? ''));

    $slug  = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $district));
    $parts = array_filter(explode('-', $slug));
    $dist  = implode('', array_map(fn($p) => $p[0] ?? '', array_slice(array_values($parts), 0, 3)));

    return $initials . '-' . $chamber . '-' . $dist;
  }

  /**
   * Extracts last name for sorting.
   */
  protected function lastName(string $name): string {
    $clean = preg_replace('/^(Representative|Senator)\s+/i', '', $name);
    $words = preg_split('/\s+/', trim($clean));
    return end($words) ?: $name;
  }

}

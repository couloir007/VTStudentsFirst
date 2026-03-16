<?php

namespace Drupal\ourkids_outreach\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Renders the legislator outreach admin page.
 */
class LegislatorOutreachController extends ControllerBase {

  /**
   * The site URL for tracked links.
   */
  const SITE_URL = 'https://ourkidsourschoolsvt.org/';

  /**
   * The ref parameter name — intentionally neutral.
   */
  const REF_PARAM = 'ref';

  /**
   * Default email subject.
   */
  const SUBJECT = "Vermont's Independent Schools and Rural Education";

  /**
   * Default email body template.
   * {tracked_link} is replaced per legislator.
   */
  const BODY_TEMPLATE = "Dear {salutation},

My name is Sean Montague. I am a constituent from Burke Hollow, Vermont, and a parent of two students, one attending St. Johnsbury Academy and one at Riverside School.

I am writing to share my concerns about the impact of current education legislation on rural Vermont communities that depend on independent schools for public secondary education. For towns like mine, tuitioning is not a policy preference, it is how public education has always been delivered.

I would welcome the opportunity to speak with you directly about how these proposals affect families in the Northeast Kingdom and across Vermont.

Learn more about our coalition: <a href=\"{tracked_link}\">OurKidsOurschoolsvt.org</a>

Thank you for your service to Vermont.

Sean Montague
Burke Hollow, VT";

  /**
   * Builds the outreach page.
   */
  public function page(Request $request): array {
    $data_path = \Drupal::service('extension.list.theme')
      ->getPath('surface') . '/data/vt-legislators-test.json';

    if (!file_exists($data_path)) {
      return [
        '#markup' => $this->t('Legislators data file not found at: @path', ['@path' => $data_path]),
      ];
    }

    $json = file_get_contents($data_path);
    $data = json_decode($json, TRUE);

    if (!$data) {
      return ['#markup' => $this->t('Could not parse legislators JSON.')];
    }

    $representatives = $data['representatives'] ?? [];
    $senators = $data['senators'] ?? [];

    return [
      '#theme' => 'ourkids_outreach_page',
      '#representatives' => $this->buildLegislatorList($representatives, 'h'),
      '#senators' => $this->buildLegislatorList($senators, 's'),
      '#attached' => [
        'library' => ['ourkids_outreach/outreach'],
      ],
    ];
  }

  /**
   * Builds a list of legislators with mailto links.
   *
   * @param array $legislators
   *   Array of legislator data from JSON.
   * @param string $chamber
   *   'h' for House, 's' for Senate.
   *
   * @return array
   *   Array of legislator arrays with name, district, email, mailto, ref.
   */
  protected function buildLegislatorList(array $legislators, string $chamber): array {
    $list = [];

    foreach ($legislators as $leg) {
      $name  = $leg['name']     ?? '';
      $email = $leg['email']    ?? '';
      $district = $leg['district'] ?? '';

      if (!$email) {
        continue;
      }

      $ref     = $this->buildRef($name, $chamber, $district);
      $tracked = self::SITE_URL . '?' . self::REF_PARAM . '=' . urlencode($ref);

      // Build salutation from name — strip "Representative" / "Senator" prefix.
      $salutation = preg_replace('/^(Representative|Senator)\s+/i', '', $name);

      $body = str_replace(
        ['{salutation}', '{tracked_link}'],
        [$salutation, $tracked],
        self::BODY_TEMPLATE
      );

      $mailto = sprintf(
        'mailto:%s?subject=%s&body=%s',
        rawurlencode($email),
        rawurlencode(self::SUBJECT),
        rawurlencode($body)
      );

      $list[] = [
        'name'     => $name,
        'district' => $district,
        'email'    => $email,
        'mailto'   => $mailto,
        'ref'      => $ref,
        'tracked'  => $tracked,
      ];
    }

    // Sort alphabetically by last name.
    usort($list, function ($a, $b) {
      $last_a = $this->lastName($a['name']);
      $last_b = $this->lastName($b['name']);
      return strcmp($last_a, $last_b);
    });

    return $list;
  }

  /**
   * Builds a short opaque ref token from name, chamber, district.
   *
   * Format: {initials}-{chamber}-{district_slug}
   * Example: sc-h-ce (Scott Campbell, House, Caledonia-Essex)
   */
  protected function buildRef(string $name, string $chamber, string $district): string {
    // Strip title prefix.
    $clean = preg_replace('/^(Representative|Senator)\s+/i', '', $name);

    // Get initials from first and last word.
    $words    = preg_split('/\s+/', trim($clean));
    $initials = '';
    if (!empty($words[0])) {
      $initials .= strtolower($words[0][0]);
    }
    if (count($words) > 1) {
      $initials .= strtolower($words[count($words) - 1][0]);
    }

    // Slugify district — take first two significant parts.
    $district_slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $district));
    $district_slug = trim($district_slug, '-');
    // Shorten to first segment e.g. caledonia-essex → ce.
    $parts = explode('-', $district_slug);
    $short_district = implode('', array_map(fn($p) => $p[0] ?? '', array_slice($parts, 0, 3)));

    return $initials . '-' . $chamber . '-' . $short_district;
  }

  /**
   * Extracts the last name from a full name string.
   */
  protected function lastName(string $name): string {
    $clean = preg_replace('/^(Representative|Senator)\s+/i', '', $name);
    $words = preg_split('/\s+/', trim($clean));
    return end($words) ?: $name;
  }

}

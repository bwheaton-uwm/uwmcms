<?php

namespace Drupal\uwmcs_reader\Controller;

/***
 * See https://www.drupal.org/docs/8/api/
 * routing-system/parameter-upcasting-in-routes
 *
 */
use Drupal\Core\Language\LanguageInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Component\Utility\Html;
use Drupal\node\NodeInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Controller routines for UWMCS JSON Reader pages.
 */
class UwmController {

  private $requestUri;

  private $requestArgs;

  /**
   * UwmController constructor.
   */
  public function __construct() {

    $this->requestUri = \Drupal::request()->getRequestUri();
    $this->requestArgs = explode('/', trim($this->requestUri, '/'));

  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function welcomePage() {

    $element = [
      '#markup' => 'Hello, world',
    ];

    return $element;
  }

  /**
   * Returns a simple page element.
   *
   * @return array
   *   A simple renderable array.
   */
  public function renderProvider() {

    $element = [];

    if ($this->requestArgs[0] === 'bios') {

      $reader = new UwmFetcher();
      $provider = $reader->searchForProvider($this->requestArgs[1]);

      if (!empty($provider->fullName)) {

        $element['#markup'] = "{$provider->fullName} ....." .
          "has the following body text:<br><br>{$provider->bioIntro}";

      }

    }

    if (empty($element)) {

      throw new NotFoundHttpException();

    }
    $node = $this->createNode(['body' => $element]);
    return $node;

  }

  /**
   * Function description.
   *
   * @param \Drupal\node\NodeInterface $node1
   *   Param description...
   * @param \Drupal\node\NodeInterface $node2
   *   Param description...
   */
  public function foo(NodeInterface $node1, NodeInterface $node2) {

    $a = 123;
    return NULL;
  }

  /**
   * Function description.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Param description...
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Param description...
   */
  public function access(AccountInterface $account) {
    // Check permissions and combine that with any custom access checking
    // needed. Pass forward parameters from the route and/or request as needed.
    return TRUE;
  }

  /**
   * Returns a simple page element.
   *
   * @return array
   *   A simple renderable array.
   */
  public function renderLocation() {

    $element = [];

    if ($this->requestArgs[0] === 'location') {

      $reader = new UwmFetcher();
      $location = $reader->searchForClinic($this->requestArgs[1]);

      if (!empty($location->name)) {

        $element['#markup'] = "{$location->name} ....." .
          "has the following address:<br><br>{$location->address}";

      }

    }

    if (!empty($element)) {
      return $element;
    }

    throw new NotFoundHttpException();

  }

  /**
   * Constructs UWMCS JSON Reader text with arguments.
   *
   * This callback is mapped to the path
   * 'uwmcs_reader/generate/{paragraphs}/{phrases}'.
   *
   * @param string $paragraphs
   *   The amount of paragraphs that need to be generated.
   * @param string $phrases
   *   The maximum amount of phrases that can be generated inside a paragraph.
   */
  public function generate($paragraphs, $phrases) {

    // Default settings.
    $config = \Drupal::config('uwmcs_reader.settings');
    // Page title and source text.
    $page_title = $config->get('uwmcs_reader.page_title');
    $source_text = $config->get('uwmcs_reader.source_text');

    $repertory = explode(PHP_EOL, $source_text);

    $element['#source_text'] = [];

    // Generate X paragraphs with up to Y phrases each.
    for ($i = 1; $i <= $paragraphs; $i++) {
      $this_paragraph = '';
      // When we say "up to Y phrases each", we can't mean "from 1 to Y".
      // So we go from halfway up.
      $random_phrases = mt_rand(round($phrases / 2), $phrases);
      // Also don't repeat the last phrase.
      $last_number = 0;
      $next_number = 0;
      for ($j = 1; $j <= $random_phrases; $j++) {
        do {
          $next_number = floor(mt_rand(0, count($repertory) - 1));
        } while ($next_number === $last_number && count($repertory) > 1);
        $this_paragraph .= $repertory[$next_number] . ' ';
        $last_number = $next_number;
      }
      // $element['#source_text'][] = SafeMarkup::checkPlain($this_paragraph);
      $element['#source_text'][] = Html::escape($this_paragraph);

    }

    // $element['#title'] = SafeMarkup::checkPlain($page_title);
    $element['#title'] = Html::escape($page_title);
    $element['#markup'] = implode('<br><br>', $element['#source_text']);
    $element['#theme'] = 'uwmcs_reader';

    return $element;
  }

  /**
   * Function desription.
   *
   * @param array $settings
   *   Parameter description.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Return description.
   */
  private function createNode(array $settings = []) {

    // Populate defaults array.
    $settings += [
      'title' => 'One, two, three',
      'changed' => REQUEST_TIME,
      'promote' => NODE_NOT_PROMOTED,
      'revision' => 1,
      'log' => '',
      'status' => NODE_PUBLISHED,
      'sticky' => NODE_NOT_STICKY,
      'type' => 'page',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
      'body' => [
        'value' => 'Foo monkey puzzle whoo' . date('r'),
        'format' => filter_default_format(),
      ],
      'field_image' => [
        'target_id' => 123,
        'alt' => 'Hello world',
        'title' => 'Goodbye world',
      ],
    ];

    // Create node object with attached file.
    // $node = \Drupal\node\Entity\Node::create($settings);
    $node = entity_create('node', $settings);
    if (!empty($settings['revision'])) {
      $node->setNewRevision();
    }
    $node->save();

    return $node;

  }

}

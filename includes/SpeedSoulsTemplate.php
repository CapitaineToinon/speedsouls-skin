<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * BaseTemplate class for SpeedSouls skin
 * @ingroup Skins
 */
class SpeedSoulsTemplate extends BaseTemplate
{
  /**
   * The twig variable to load templates
   */
  private $twig;

  /**
   * ctor
   */
  public function __construct()
  {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
    $this->twig = new \Twig\Environment($loader, [
      'cache' => __DIR__ . '/../../../cache',
      'debug' => true,
    ]);
  }

  /**
   * Outputs the entire contents of the page
   */
  public function execute()
  {
    $this->data['namespace_urls'] = $this->data['content_navigation']['namespaces'];
    $this->data['view_urls'] = $this->data['content_navigation']['views'];
    $this->data['action_urls'] = $this->data['content_navigation']['actions'];
    $this->data['variant_urls'] = $this->data['content_navigation']['variants'];
    $template = $this->twig->load('index.html');

    $options = [
      'htmlHeader' => $this->get('headelement'),
      'htmlNavbar' => $this->getNavbar(),
      'htmlLeftMenu' => $this->getLeftMenu(),
      'htmlBody' => $this->getBody(),
      'htmlFooter' => $this->getFooter(),
      'htmlEnd' => '</body></html>' // close tags opened by $this->get('headelement')
    ];

    // Only line that should echo the final html
    echo $template->render($options);
  }

  /**
   * getMain
   */
  public function getBody()
  {
    $template = $this->twig->load('body.html');

    $options = [
      'body' => $this->get('bodycontent')
    ];

    return $template->render($options);
  }

  /**
   * Get Left Menu
   */
  public function getLeftMenu()
  { 
    $template = $this->twig->load('leftMenu.html');

    $options = [
      'portals' => $this->renderPortals($this->data['sidebar'])
    ];

    return $template->render($options);
  }

  public function renderSection($content)
  {
    return Html::rawElement(
      'div',
      ['class' => 'section'],
      $content
    );
  }

  public function renderPortal($name, $elements, $msg = null, $hook = null)
  {
    if ($msg === null) {
      $msg = $name;
    }
    $msgObj = $this->getMsg($msg);
    $labelId = Sanitizer::escapeIdForAttribute("p-$name-label");

    $html = '';
    $html .= Html::rawElement(
      'p',
      ['class' => 'menu-label', 'id' => $labelId],
      $msgObj->exists() ? $msgObj->text() : $msg
    );

    $html .= Html::openElement(
      'ul',
      ['class' => 'menu-list', 'id' => "p-$name"]
    );

    foreach ($elements as $key => $element) {
      $html .= $this->makeListItem($key, $element);
    }

    if ($hook !== null) {
      // Avoid PHP 7.1 warning
      $skin = $this;
      Hooks::run($hook, [&$skin, true]);
    }

    $html .= Html::closeElement('ul');
    return $html;
  }

  public function renderPortals(array $portals)
  {
    // Force the rendering of the following portals
    if (!isset($portals['TOOLBOX'])) {
      $portals['TOOLBOX'] = true;
    }
    if (!isset($portals['LANGUAGES'])) {
      $portals['LANGUAGES'] = true;
    }


    $html = '';

    // Render portals
    foreach ($portals as $name => $content) {
      if ($content === false) {
        continue;
      }

      // Numeric strings gets an integer when set as key, cast back - T73639
      $name = (string) $name;

      switch ($name) {
        case 'SEARCH':
          break;
        case 'TOOLBOX':
          $html .= $this->renderPortal('tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd');
          break;
        case 'LANGUAGES':
          if ($this->data['language_urls'] !== false) {
            $html .= $this->renderPortal('lang', $this->data['language_urls'], 'otherlanguages');
          }
          break;
        default:
          $html .= $this->renderPortal($name, $content);
          break;
      }
    }

    return $html;
  }

  public function renderNamespaces()
  {
    return $this->renderTabs($this->data['namespace_urls'], 'is-boxed');
  }

  public function renderViews()
  {
    return $this->renderTabs($this->data['view_urls'], null, 'is-right');
  }

  public function renderTabs($elements = [], $style = '', $alignment = '')
  {
    $html = '';

    if (count($elements)) {
      $tabs = '';
      foreach ($elements as $key => $item) {
        if (strpos($item['class'], 'selected') !== false) {
          $item['class'] .= ' is-active';
        }

        $tabs .= $this->makeListItem($key, $item, [
          'vector-wrap' => true,
        ]);
      }

      $html .= Html::rawElement(
        'div',
        ['class' => "tabs $style $alignment"],
        Html::rawElement(
          'ul',
          [],
          $tabs
        )
      );
    }

    return $html;
  }

  /**
   * getNavbar
   */
  protected function getNavbar()
  {
    $template = $this->twig->load('components/Navbar.html');
    $personalToolsDropdown = $this->getPersonalTools();

    $options = [
      'logo' => [
        'src' => 'https://bulma.io/images/bulma-logo.png',
        'width' => '112',
        'height' => '28',
        'href' => '/',
      ],
      'navbarEnd' => $personalToolsDropdown
    ];

    return $template->render($options);
  }

  /**
   * Creates the user menu in the navbar
   */
  public function getPersonalTools($isHoverable = true)
  {
    $template = $this->twig->load('components/PersonalToolsDropdown.html');
    $items = parent::getPersonalTools();

    $defaultKey = array_key_exists('login', $items) ? 'login' : 'userpage';
    $default = $items[$defaultKey];
    unset($items[$defaultKey]);

    $options = [
      'default' => $default,
      'items' => $items
    ];

    return $template->render($options);
  }

  protected function renderNavigation(array $elements)
  { }


  protected function getFooter()
  {
    $html = '<footer class="footer">
      <div class="content has-text-centered">
        <p>
          <strong>Bulma</strong> by <a href="https://jgthms.com">Jeremy Thomas</a>. The source code is licensed
          <a href="http://opensource.org/licenses/mit-license.php">MIT</a>. The website content
          is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.
        </p>
      </div>
    </footer>';

    // $html = '';

    // $html .= Html::rawElement(
    //   'footer',
    //   ['class' => 'footer'],
    //   'lmao'
    // );

    return $html;
  }
}

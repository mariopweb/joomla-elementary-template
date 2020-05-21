<?php

use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

// $this to
// Joomla\CMS\Document\HtmlDocumen

// Joomla\CMS\Document\HtmlDocument
$doc = Factory::getDocument();
$app = Factory::getApplication();

/**
 * @return Joomla\Registry\Registry
 */
$params = $app->getTemplate(true)->params;

// template params
$themeColor = $params->get('themecolor');
if ($params->get('sitename')) {
    $siteName = htmlspecialchars($params->get('sitename'));
}
if ($params->get('sitedescription')) {
    $siteDesc = htmlspecialchars($params->get('sitedescription'));
}
if ($params->get('sitelogo')) {
    # code...
    $logoSrc = Uri::base() . htmlspecialchars($params->get('sitelogo'));
}
// J! icons
$doc->addStyleSheet($this->baseurl . '/media/jui/css/icomoon.css');
// add stylesheet
HTMLHelper::_('stylesheet', 'template.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', $themeColor . '.css', array('relative' => true, 'version' => 'auto'));
// menu stylesheet
HTMLHelper::_('stylesheet', 'sm-core-css.css', array('relative' => true, 'version' => 'auto'));
// js
HTMLHelper::_('script', 'template.js', array('version' => 'auto', 'relative' => true));
// menu js
HTMLHelper::_('script', 'jquery.smartmenus.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'jquery.smartmenus.bootstrap-4.js', array('version' => 'auto', 'relative' => true));

// custom
HTMLHelper::_('stylesheet', 'user.css', array('version' => 'auto', 'relative' => true));

$spanPromoMd = "";
if ($this->countModules('promo-left') && $this->countModules('promo-right')) {
    # code...
    $spanPromoMd = 'md-6';
} elseif ($this->countModules('promo-left') && !$this->countModules('promo-right')) {
    # code...
    $spanPromoMd = 'md-9';
} elseif (!$this->countModules('promo-left') && $this->countModules('promo-right')) {
    $spanPromoMd = 'md-9';
} else {
    $spanPromoMd = 'md-12';
}
$spanMd = "";
if (($this->countModules('left-menu') || $this->countModules('mainbody-left')) && ($this->countModules('mainbody-right') || $this->countModules('right-menu'))) {
    # code...
    $spanMd = 'md-6';
} elseif (($this->countModules('left-menu') || $this->countModules('mainbody-left')) && (!$this->countModules('mainbody-right') && !$this->countModules('right-menu'))) {
    # code...
    $spanMd = 'md-9';
} elseif ((!$this->countModules('left-menu') && !$this->countModules('mainbody-left')) && ($this->countModules('mainbody-right') || $this->countModules('right-menu'))) {
    $spanMd = 'md-9';
} else {
    $spanMd = 'md-12';
}
$spanSm = "";
if (!$this->countModules('footer')) {
    $spanSm = "sm-6";
} else {
    $spanSm = "sm-4";
}

?>


<!doctype html>
<html lang="<?php echo $this->language; ?>">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <jdoc:include type="head" />

    <!-- Bootstrap CSS -->
    <?php if ($this->direction === 'rtl') : ?>
        <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css" integrity="sha384-vus3nQHTD+5mpDiZ4rkEPlnkcyTP+49BhJ4wJeJunw06ZAp+wzzeBPUXr42fi8If" crossorigin="anonymous">
    <?php else : ?>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <?php endif; ?>

</head>

<body>
    <!-- top -->
    <div id="top" class="container-fluid text-white">
        <div class="row">
            <div class="top-inner col-md-6 d-flex justify-content-start">
                <div class="top-module">
                    <?php if ($params->get('sitename')) : ?>
                        <h5><a href="<?php echo Uri::base(); ?>"><?php echo $siteName; ?></a></h5>
                    <?php endif; ?>
                </div>
            </div>
            <div class="top-inner col-md-6 d-flex justify-content-end">
                <div class="top-module">
                    <jdoc:include type="modules" name="search" style="xhtml" />
                </div>
            </div>
        </div>
    </div>
    <!--main body wrapper -->
    <div id="wrapper" class="container">
        <!-- header -->
        <header id="header">
            <div class="header-inner bg-white clearfix">
                <div class="header-brand float-left">
                    <div class="card">
                        <?php if ($params->get('sitelogo')) : ?>
                            <img class="card-img-top" src="<?php echo $logoSrc; ?>" alt="Logo">
                        <?php endif; ?>
                        <div class="card-body">
                            <?php if ($params->get('sitedescription')) : ?>
                                <h3><?php echo $siteDesc; ?></h3>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="header-search float-right">
                    <jdoc:include type="modules" name="header-right" style="xhtml" />
                </div>
            </div>
        </header>
        <!-- nav -->
        <?php if ($this->countModules('main-menu')) : ?>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbarSupporterContent" aria-controls="mainNavbarSupporterContent" aria-expanded="false" aria-label="Main menu toggler button">
                    Menu
                </button>
                <div class="collapse navbar-collapse" id="mainNavbarSupporterContent">
                    <jdoc:include type="modules" name="main-menu" style="none" />
                </div>
            </nav>
        <?php endif; ?>
        <!-- promo -->
        <div class="row">
            <?php if ($this->countModules('promo-left')) : ?>
                <div id="promo-left" class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <jdoc:include type="modules" name="promo-left" style="xhtml" />
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($this->countModules('promo')) : ?>
                <div id="promo" class="col-<?php echo $spanPromoMd; ?>">
                    <div class="card">
                        <div class="card-body">
                            <jdoc:include type="modules" name="promo" style="xhtml" />
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($this->countModules('promo-right')) : ?>
                <div id="promo-right" class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <jdoc:include type="modules" name="promo-right" style="xhtml" />
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($this->countModules('content-top')) : ?>
            <div class="row">
                <div id="content-top" class="col-md-12">
                    <jdoc:include type="modules" name="content-top" style="xhtml" />
                </div>
            </div>
        <?php endif; ?>
        <!-- mainbody content -->
        <main class="mainbody">
            <div class="row">
                <?php if ($this->countModules('left-menu') || $this->countModules('mainbody-left')) : ?>
                    <div id="mainbody-left" class="col-md-3">
                        <?php if ($this->countModules('left-menu')) : ?>
                            <nav class="navbar navbar-expand-lg navbar-dark">
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#leftNavbarSupporterContent" aria-controls="leftNavbarSupporterContent" aria-expanded="false" aria-label="Left menu toggler button">
                                    Menu
                                </button>
                                <div class="collapse navbar-collapse" id="leftNavbarSupporterContent">
                                    <jdoc:include type="modules" name="left-menu" style="xhtml" />
                                </div>
                            </nav>
                        <?php endif; ?>
                        <?php if ($this->countModules('mainbody-left')) : ?>
                            <jdoc:include type="modules" name="mainbody-left" style="xhtml" />
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div id="mainbody-content" role="main" class="col-<?php echo $spanMd; ?>">
                    <!-- breadcrumps -->
                    <?php if ($this->countModules('breadcrumbs')) : ?>
                        <jdoc:include type="module" name="breadcrumbs" style="xhtml" />
                    <?php endif; ?>
                    <!-- user-top -->
                    <?php if ($this->countModules('user-top')) : ?>
                        <jdoc:include type="module" name="user-top" style="xhtml" />
                    <?php endif; ?>
                    <!-- main content -->
                    <jdoc:include type="message" />
                    <jdoc:include type="component" />
                    <!-- user-bottom -->
                    <?php if ($this->countModules('user-bottom')) : ?>
                        <jdoc:include type="module" name="user-bottom" style="xhtml" />
                    <?php endif; ?>
                </div>
                <?php if ($this->countModules('right-menu') || $this->countModules('mainbody-right')) : ?>
                    <div id="mainbody-right" class="col-md-3">
                        <?php if ($this->countModules('right-menu')) : ?>
                            <nav class="navbar navbar-expand-lg navbar-dark">
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#rightNavbarSupporterContent" aria-controls="rightNavbarSupporterContent" aria-expanded="false" aria-label="Right menu toggler button">
                                    Menu
                                </button>
                                <div class="collapse navbar-collapse" id="rightNavbarSupporterContent">
                                    <jdoc:include type="modules" name="right-menu" style="xhtml" />
                                </div>
                            </nav>
                        <?php endif; ?>
                        <?php if ($this->countModules('mainbody-right')) : ?>
                            <jdoc:include type="modules" name="mainbody-right" style="xhtml" />
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
        <?php if ($this->countModules('content-bottom')) : ?>
            <div class="row">
                <div id="content-bottom" class="col-md-12">
                    Full width content-bottom div
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div id="bottom" class="container-fluid">
        <!-- footer -->
        <footer>
            <div class="row">
                <div class="col-<?php echo $spanSm; ?> d-none d-md-block">
                    <p>
                        &copy; <?php echo date('Y') . " " . ($params->get('sitename') ? $siteName : ''); ?>
                    </p>
                </div>
                <?php if ($this->countModules('footer')) : ?>
                    <div class="col-sm-4">
                        <jdoc:include type="modules" name="footer" style="xhtml" />
                    </div>
                <?php endif; ?>
                <div class="col-<?php echo $spanSm; ?> d-none d-md-block">
                    <p class="text-right">
                        <a href="#top" id="back-top">
                            <span class="icon-arrow-up large-icon"></span><?php echo Text::_('TPL_ELEMENTARY_BACKTOTOP'); ?>
                        </a>
                    </p>
                </div>
            </div>
        </footer>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>
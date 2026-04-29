<?php
$page_title = 'Insights | Distinguished Real Estate';
$page_key = 'insights';
$body_class = 'insights-page';
$meta_description = 'News and insights from Distinguished Real Estate — Dubai property market updates, company announcements, and guides.';
$style_version = '58';
require __DIR__ . '/includes/header.php';

$insights_items = [
    [
        'href' => '#',
        'image' => 'public/images/news/1.jpg',
        'date' => '2014-05-27',
        'date_label' => 'May 27, 2014',
        'title' => 'The Ultimate Guide to First-Time Homebuyers',
        'excerpt' => 'Business meeting of real estate broker, Business meeting working with new startup project. Idea presentation analyze plan.',
    ],
    [
        'href' => '#',
        'image' => 'public/images/news/2.jpg',
        'date' => '2014-05-27',
        'date_label' => 'May 27, 2014',
        'title' => "Distinguished Real Estate Selects Yardi's Unified Cloud Solution...",
        'excerpt' => 'Dubai-based real estate owner and operator to utilize a cloud-based solution to expand commercial and residential portfolio operations',
    ],
    [
        'href' => '#',
        'image' => 'public/images/news/1.jpg',
        'date' => '2014-05-27',
        'date_label' => 'May 27, 2014',
        'title' => 'Distinguished Real Estate Partners With Keys Please Holiday',
        'excerpt' => 'Distinguished Real Estate (DRE), one of the largest fully integrated real estate developers in the UAE announced its partnership...',
    ],
    [
        'href' => '#',
        'image' => 'public/images/news/1.jpg',
        'date' => '2014-05-27',
        'date_label' => 'May 27, 2014',
        'title' => 'The Ultimate Guide to First-Time Homebuyers',
        'excerpt' => 'Business meeting of real estate broker, Business meeting working with new startup project. Idea presentation analyze plan.',
    ],
    [
        'href' => '#',
        'image' => 'public/images/news/2.jpg',
        'date' => '2014-05-27',
        'date_label' => 'May 27, 2014',
        'title' => "Distinguished Real Estate Selects Yardi's Unified Cloud Solution...",
        'excerpt' => 'Dubai-based real estate owner and operator to utilize a cloud-based solution to expand commercial and residential portfolio operations',
    ],
    [
        'href' => '#',
        'image' => 'public/images/news/1.jpg',
        'date' => '2014-05-27',
        'date_label' => 'May 27, 2014',
        'title' => 'Distinguished Real Estate Partners With Keys Please Holiday',
        'excerpt' => 'Distinguished Real Estate (DRE), one of the largest fully integrated real estate developers in the UAE announced its partnership...',
    ],
];
?>
    <section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img src="public/images/inner-banner.jpg" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">Insights</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li>
                        <a href="index.php" aria-label="Home">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">Insights</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="insights-section commonPadding-120" aria-labelledby="insights-heading">
        <div class="container-ctn">
            <h2 id="insights-heading" class="visually-hidden">Latest articles</h2>
            <ul class="insights-grid">
                <?php foreach ($insights_items as $item) : ?>
                    <li>
                        <article class="insight-card">
                            <a class="insight-card__link" href="insights-details.php">
                                <div class="insight-card__media">
                                    <div class="insight-card__image-wrap">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" width="480" height="270" loading="lazy">
                                    </div>
                                    <time class="insight-card__date" datetime="<?php echo htmlspecialchars($item['date']); ?>">
                                        <span class="insight-card__date-inner">
                                            <span class="insight-card__date-text"><?php echo htmlspecialchars($item['date_label']); ?></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M9.24546 20.7424V3.2494M4.74725 16.6331H2.74805M4.74725 7.38675H2.74805M4.74725 6.74801L4.74725 17.2438C4.74725 18.1717 5.11585 19.0616 5.77197 19.7177C6.42808 20.3738 7.31797 20.7424 8.24586 20.7424H17.7421C18.67 20.7424 19.5598 20.3738 20.216 19.7177C20.8721 19.0616 21.2407 18.1717 21.2407 17.2438V6.74801C21.2407 5.82012 20.8721 4.93023 20.216 4.27412C19.5598 3.618 18.67 3.2494 17.7421 3.2494H8.24586C7.31797 3.2494 6.42808 3.618 5.77197 4.27412C5.11585 4.93023 4.74725 5.82012 4.74725 6.74801Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>                              </span>
                                    </time>
                                </div>
                                <div class="insight-card__body">
                                    <h3 class="insight-card__title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p class="insight-card__excerpt"><?php echo htmlspecialchars($item['excerpt']); ?></p>
                                </div>
                                <span class="insight-card__arrow" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 17L17 7M17 7H9M17 7V15"/>
                                    </svg>
                                </span>
                            </a>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/modal-enquiry.php';

require __DIR__ . '/includes/floating-widgets.php';
require __DIR__ . '/includes/scripts.php';

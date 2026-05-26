<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title>Sitemap Crawl Links</title>
</head>
<body>
    <main>
        <h1>Sitemap Crawl Links</h1>

        <ul>
            @foreach($urls as $url)
                <li><a href="{{ $url }}">{{ $url }}</a></li>
            @endforeach
        </ul>
    </main>
</body>
</html>

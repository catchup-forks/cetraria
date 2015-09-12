{{ get_doctype() }}
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    {{ get_title() }}
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <link rel="icon" href="/favicon.ico?v=0.1" sizes="16x16 32x32 48x48 64x64" type="image/vnd.microsoft.icon">
    {{- assets.outputCss() -}}
</head>
<body>
    {{ content() }}
    {{- assets.outputJs() -}}
</body>
</html>

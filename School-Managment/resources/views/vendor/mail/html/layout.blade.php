<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{{-- The design is already dark: clients that follow this don't invert its colors --}}
<meta name="color-scheme" content="dark light">
<meta name="supported-color-schemes" content="dark light">
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
:root {
color-scheme: dark light;
supported-color-schemes: dark light;
}

@media only screen and (max-width: 640px) {
.header-inner,
.inner-body,
.footer {
width: 100% !important;
}

.content-cell {
padding: 28px 22px !important;
}

.content-cell h1 {
font-size: 28px !important;
}
}

@media only screen and (max-width: 500px) {
.action table {
width: 100% !important;
}

.button {
text-align: center !important;
width: 100% !important;
}
}
</style>
{!! $head ?? '' !!}
</head>
<body bgcolor="#070B1A">

<table class="wrapper" width="100%" bgcolor="#070B1A" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="wrapper-cell" align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="600" bgcolor="#0C1330" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>

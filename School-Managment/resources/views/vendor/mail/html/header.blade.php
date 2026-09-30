@props(['url'])
{{-- "cid:logo" is the logo attached to the email (AppServiceProvider), so it shows
     without the mail client loading anything from the server --}}
<tr>
<td class="header">
<table class="header-inner" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="brand-logo" width="40">
<a href="{{ $url }}" target="_blank" rel="noopener"><img src="cid:logo" class="logo" width="40" height="40" alt=""></a>
</td>
<td class="brand-name">
<a href="{{ $url }}" target="_blank" rel="noopener">{!! trim($slot) !!}</a><span class="cursor">&nbsp;</span>
</td>
</tr>
</table>
</td>
</tr>

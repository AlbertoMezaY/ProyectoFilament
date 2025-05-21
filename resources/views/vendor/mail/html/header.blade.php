@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img 
    src="https://hunabku.mx/img/logo-white@2x.png" 
    alt="Hunabku Logo" 
    style="height: 60px; width: auto; display: block; margin: 0 auto;"
>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>

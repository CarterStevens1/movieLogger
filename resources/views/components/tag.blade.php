 @props(['tag', 'size' => 'base'])

 @php
     if ($size === 'base') {
         $classes = ' text-sm px-5 py-1';
     } elseif ($size === 'small') {
         $classes = ' text-2xs px-3 py-1';
     }
 @endphp


 <span {{ $attributes->merge(['style' => 'background-color: ' . $tag['color'], 'class' => $classes]) }}>
     {{ $tag['name'] }}
 </span>

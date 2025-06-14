 @props(['tag', 'size' => 'base'])

 @php

     $colors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500'];
     $randomColor = $colors[array_rand($colors)];
     $classes = $randomColor . ' text-white rounded-xl font-bold transition-colors duration-300';

     if ($size === 'base') {
         $classes .= ' text-sm px-5 py-1';
     } elseif ($size === 'small') {
         $classes .= ' text-2xs px-3 py-1';
     }
 @endphp

 <span {{ $attributes(['class' => $classes]) }}>{{ $tag }}</span>

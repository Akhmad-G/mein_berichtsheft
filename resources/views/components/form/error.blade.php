@props(['messages' => []])

@if ($messages)
    <ul class="flex flex-col gap-1 text-[12.5px] text-stamp">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

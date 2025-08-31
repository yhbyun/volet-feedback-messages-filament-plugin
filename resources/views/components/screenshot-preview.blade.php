<div class="screenshot-preview">
    @if($url)
        <img
            src="{{ $url }}"
            alt="{{ $filename }}"
            class="max-w-full h-auto rounded-lg border border-gray-200 shadow-sm"
            style="max-height: 200px;"
        />
    @else
        <div class="text-gray-500 text-sm">
            No preview available
        </div>
    @endif
</div>

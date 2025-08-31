<div class="screenshot-container">
    <img
        src="{{ $url }}"
        class="h-auto rounded-lg shadow-sm"
        style="max-height: 100px; object-fit: contain;"
        onclick="window.open(this.src, '_blank')"
        title="클릭하면 원본 크기로 보기"
    />
</div>

<style>
.screenshot-container img {
    cursor: pointer;
    transition: transform 0.2s;
}
.screenshot-container img:hover {
    transform: scale(1.02);
}
</style>

/*------------------------------------------------
    ツールチップの表示にかかわる処理
------------------------------------------------*/
document.addEventListener('DOMContentLoaded', function() {
    var tooltips = document.querySelectorAll('.tooltip .tooltiptext');
    tooltips.forEach(function(tooltip) {
        var parent = tooltip.parentElement;
        parent.addEventListener('mouseenter', function() {
            var rect = tooltip.getBoundingClientRect();
            var margin = 200; // 余裕を持たせるためのマージン

            // ツールチップが画面内に収まるように位置を調整
            if (rect.right + margin > window.innerWidth) {
                tooltip.style.left = 'auto';
                tooltip.style.right = margin + 'px';
            }
            if (rect.left - margin < 0) {
                tooltip.style.left = margin + 'px';
                tooltip.style.right = 'auto';
            }
            if (rect.bottom + margin > window.innerHeight) {
                tooltip.style.top = 'auto';
                tooltip.style.bottom = (125 + margin) + '%';
            }
            if (rect.top - margin < 0) {
                tooltip.style.top = (125 + margin) + '%';
                tooltip.style.bottom = 'auto';
            }
        });
    });
});
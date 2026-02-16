<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>GrowthSense AI Dashboard</h1>

    <!-- Demo Metrics -->
    <div style="display:flex; gap:30px; flex-wrap:wrap; margin-bottom:20px;">
        <div style="flex:1; min-width:200px; background:#f0f4f8; padding:15px; border-radius:10px; text-align:center; box-shadow:1px 1px 6px rgba(0,0,0,0.05);">
            <h2 style="margin:0; color:#0073aa;"><?php echo esc_html($product_count); ?></h2>
            <p style="margin:0; color:#555;">Products</p>
        </div>
        <div style="flex:1; min-width:200px; background:#f0f4f8; padding:15px; border-radius:10px; text-align:center; box-shadow:1px 1px 6px rgba(0,0,0,0.05);">
            <h2 style="margin:0; color:#0073aa;"><?php echo esc_html($order_count); ?></h2>
            <p style="margin:0; color:#555;">Completed Orders</p>
        </div>
    </div>

    <!-- Analyze Button -->
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-bottom:20px;">
        <input type="hidden" name="action" value="gs_analyze">
        <?php wp_nonce_field('gs_analyze_nonce'); ?>
        <button class="button button-primary" type="submit" style="padding:10px 25px; font-size:16px;">Analyze Latest Posts</button>
    </form>

    <!-- Search -->
    <input type="text" id="gs-search" placeholder="Search posts..." style="width:100%; padding:8px 12px; margin-bottom:20px; border-radius:5px; border:1px solid #ccc;">

    <!-- Tabs -->
    <div style="margin-bottom:15px;">
        <button class="gs-tab active" data-target="all">All Posts</button>
        <button class="gs-tab" data-target="insights">Insights Only</button>
    </div>

    <?php if (!empty($results)): ?>
        <div id="gs-posts" style="display:flex; flex-wrap:wrap; gap:20px;">
            <?php foreach ($results as $insight): ?>
                <?php
                    $post = get_post($insight->post_id);
                    if (!$post) continue;

                    // Random demo AI confidence
                    $confidence = ['High','Medium','Low'][rand(0,2)];
                    $badge_color = $confidence === 'High' ? '#28a745' : ($confidence === 'Medium' ? '#ffc107' : '#dc3545');
                ?>
                <div class="gs-card" data-title="<?php echo esc_attr(strtolower($post->post_title)); ?>" style="flex:1 1 calc(50% - 20px); background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s;">
                    <h3 style="margin-top:0; color:#0073aa;"><?php echo esc_html($post->post_title); ?></h3>
                    <p style="font-size:13px; color:#888;"><?php echo esc_html(date('M d, Y', strtotime($insight->created_at))); ?></p>
                    <p style="margin-top:10px; line-height:1.5; color:#333;">
                        <span style="font-weight:bold; color:#222;">💡 AI Insight:</span> <?php echo esc_html($insight->insight); ?>
                    </p>
                    <span style="display:inline-block; background:<?php echo $badge_color; ?>; color:#fff; padding:2px 8px; border-radius:5px; font-size:12px;">Confidence: <?php echo $confidence; ?></span>
                    <button class="gs-copy" style="float:right; padding:2px 6px; font-size:12px; margin-top:-20px;">Copy</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No insights generated yet. Click the button above to analyze posts.</p>
    <?php endif; ?>
</div>

<!-- JS for Search, Tabs, and Copy -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy buttons
    document.querySelectorAll('.gs-copy').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.parentElement.querySelector('p').innerText;
            navigator.clipboard.writeText(text);
            alert('Copied insight!');
        });
    });

    // Search filter
    const searchInput = document.getElementById('gs-search');
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.gs-card').forEach(card => {
            const title = card.dataset.title;
            card.style.display = title.includes(term) ? 'block' : 'none';
        });
    });

    // Tabs
    const tabs = document.querySelectorAll('.gs-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t=>t.classList.remove('active'));
            this.classList.add('active');
            const target = this.dataset.target;
            document.querySelectorAll('.gs-card').forEach(card => {
                if(target==='all') card.style.display = 'block';
                else if(target==='insights') card.style.display = card.querySelector('p').innerText ? 'block' : 'none';
            });
        });
    });
});
</script>

<style>
.gs-tab {
    background:#f0f4f8; border:1px solid #ccc; padding:6px 15px; border-radius:5px; margin-right:5px; cursor:pointer;
}
.gs-tab.active {
    background:#0073aa; color:#fff; border-color:#0073aa;
}
.gs-card:hover {
    transform:translateY(-5px);
    box-shadow:0 8px 20px rgba(0,0,0,0.12);
}
</style>

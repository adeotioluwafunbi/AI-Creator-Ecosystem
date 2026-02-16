<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>Ecosystem AI Dashboard</h1>

    <!-- Summary Cards -->
    <div class="eai-summary-cards">
        <div class="eai-card">
            <div class="eai-card-title">Posts</div>
            <div class="eai-card-value"><?php echo wp_count_posts()->publish; ?></div>
        </div>
        <div class="eai-card">
            <div class="eai-card-title">Users</div>
            <div class="eai-card-value"><?php echo count_users()['total_users']; ?></div>
        </div>
        <div class="eai-card">
            <div class="eai-card-title">Comments</div>
            <div class="eai-card-value"><?php echo wp_count_comments()->approved; ?></div>
        </div>
        <div class="eai-card">
            <div class="eai-card-title">Plugins</div>
            <div class="eai-card-value"><?php echo count(get_plugins()); ?></div>
        </div>
    </div>

    <!-- Charts -->
    <div class="eai-charts">
        <canvas id="eai-bar-chart" height="150"></canvas>
        <canvas id="eai-pie-chart" height="150"></canvas>
    </div>

    <!-- Insights Grid -->
    <input type="text" id="eai-search" placeholder="Search insights..." class="eai-search-bar">
    <div id="eai-insights" class="eai-insights-grid">
        <?php foreach ($results as $insight): ?>
            <div class="eai-insight-card" data-category="<?php echo esc_attr($insight->category ?? 'General'); ?>">
                <p><?php echo esc_html($insight->insight); ?></p>
                <span class="eai-date"><?php echo esc_html(date('M d, Y', strtotime($insight->created_at))); ?></span>
                <button class="eai-copy">Copy</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

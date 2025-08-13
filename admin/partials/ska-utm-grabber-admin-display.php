<?php
// Check user capabilities
if (! current_user_can('manage_options')) {
    return;
}

// Show error/update messages
settings_errors('utmGrabber_messages');
?>

<?php wp_nonce_field('utm_grabber_nonce_action', 'utm_grabber_nonce'); ?>
<div class="wrap">
    <!-- <h1><?php echo esc_html(get_admin_page_title()); ?></h1> -->

    <h1>User Guide</h1>
    <div class="utm-grabber-guide">
        <h2>How to Use SearchKings Africa UTM Grabber</h2>

        <h3>1. Form Field Population</h3>
        <p>The plugin automatically populates form fields with UTM data. Add these hidden fields to your forms:</p>
        <ul>
            <li><code>utm_source</code>,<code>utm_campaign</code>,<code>utm_medium</code>, <code>utm_content</code></li>
            <li><code>utm_term</code>,<code>device</code>, <code>keyword</code>, <code>network</code>, <code>placement</code></li>
            <li><code>adposition</code>,<code>gclid</code>, <code>channel</code>, <code>source</code></li>
        </ul>
        <p>Example for a standard form:</p>
        <pre>
&lt;form&gt;
    &lt;input type="hidden" name="utm_source" value=""&gt;
    &lt;input type="hidden" name="utm_campaign" value=""&gt;
    &lt;input type="hidden" name="utm_medium" value=""&gt;
    &lt;input type="hidden" name="utm_content" value=""&gt;
    &lt;input type="hidden" name="utm_term" value=""&gt;
    &lt;!-- Add other UTM fields similarly --&gt;
    &lt;input type="hidden" name="device" value=""&gt;
    &lt;input type="hidden" name="keyword" value=""&gt;
    &lt;input type="hidden" name="network" value=""&gt;
    &lt;input type="hidden" name="placement" value=""&gt;
    &lt;input type="hidden" name="adposition" value=""&gt;
    &lt;input type="hidden" name="gclid" value=""&gt;
    &lt;input type="hidden" name="channel" value=""&gt;
    &lt;input type="hidden" name="source" value=""&gt;
&lt;/form&gt;
        </pre>
        <p>For Elementor forms, add hidden fields with the same names.</p>

        <h3>2. Traffic Source Detection</h3>
        <p>The plugin automatically detects and populates the following information:</p>
        <ul>
            <li><strong>Channel:</strong> Paid Search, Organic, Social, Email, or Other</li>
            <li><strong>Source:</strong> Google, Bing, Yahoo, or other sources as detected</li>
        </ul>
        <p>This information is added to the <code>channel</code> and <code>source</code> form fields when present.</p>
    </div>
</div>

<style>
    .utm-grabber-guide {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .utm-grabber-guide h3 {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .utm-grabber-guide h3 {
        margin-top: 20px;
    }

    .utm-grabber-guide code {
        background: #f4f4f4;
        padding: 2px 5px;
        border-radius: 3px;
    }

    .utm-grabber-guide pre {
        background: #f4f4f4;
        padding: 10px;
        border-radius: 3px;
        overflow-x: auto;
    }
</style>
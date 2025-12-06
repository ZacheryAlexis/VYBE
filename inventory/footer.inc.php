<div style="padding: 20px 30px; background: linear-gradient(135deg, #0E0E11 0%, #1A1A1E 100%); color: var(--vybe-muted); line-height: 1.8; border-top: 1px solid rgba(199,185,255,0.3);">
    <p style="margin: 0 0 8px 0; font-weight: 600; color: white; font-family: 'Avenir', 'Avenir Next', sans-serif;">&copy; <?php echo date('Y'); ?> Vybe — Campus Scents. All rights reserved.</p>
    <p style="margin: 0 0 8px 0; font-family: 'Avenir', 'Avenir Next', sans-serif;">Designed for students. Questions? <a href="mailto:hello@vybe.co" style="color: var(--vybe-orange); text-decoration: none;">hello@vybe.co</a></p>
    <p style="margin: 0; font-size: 0.9rem; color: rgba(255,255,255,0.5);">
        <?php
        date_default_timezone_set("America/New_York");
        echo "Local time: " . date("D M j, Y \a\t g:i A T");
        ?>
    </p>
</div>

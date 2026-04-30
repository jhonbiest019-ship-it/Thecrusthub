# Bake Delight Live Deployment Checklist

## 1) Install WordPress on Hosting
- Open your hosting panel (cPanel/Hostinger).
- Create domain or subdomain for the website.
- Install a fresh WordPress instance.

## 2) Upload Plugin Package
- Login to WordPress admin.
- Go to `Plugins > Add New > Upload Plugin`.
- Upload `bake-delight.zip`.
- Click `Install Now`, then `Activate`.

## 3) Verify Plugin Pages
- Open `/bake-admin` while logged in as admin.
- Open `/bake-store` on frontend.
- Confirm both pages render.

## 4) Security and Access Validation
- Confirm non-admin cannot access `/bake-admin`.
- Confirm admin can access `/bake-admin`.

## 5) Functional Validation
- Place one test order from storefront flow.
- Confirm WhatsApp opens with structured order text.
- Confirm delivery date/time is at least 24 hours ahead.

## 6) Final QA
- Check mobile responsiveness.
- Check branding footer text on all plugin pages.
- Re-test after permalink save from `Settings > Permalinks`.

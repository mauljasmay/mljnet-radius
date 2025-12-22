# Auto-Update Feature Implementation

## Overview
Add auto-update functionality via GitHub that checks for new tags, shows toast notifications on admin dashboard, and allows manual triggering of updates with auto-install and setup.

## Steps
- [x] Create GitHubUpdateService to fetch latest tag from GitHub API
- [x] Add GitHub repo URL setting in AppSetting
- [x] Modify DashboardController to check for updates on index() and projectUpdates()
- [x] Update admin dashboard view to show update notification/toast if new version available
- [x] Add update button to project-updates page
- [x] Create route for triggering update
- [x] Implement update logic: git pull, composer install, npm install, migrate, etc.
- [x] Add auto-setup after update
- [x] Test the update process
- [x] Set up GitHub repository URL in .env file (GITHUB_REPO_URL=https://github.com/mauljasmay/mljnet-radius)

# TODO: Change Docker port from 8080 to 80/443

## Steps to Complete
- [x] Update docker-compose.yml: Change ports to "80:80" and "443:443", remove obsolete version
- [x] Update Dockerfile: Add command to generate self-signed SSL certificates for development
- [x] Update docker/nginx/default.conf: Add server block for port 443 with SSL configuration
- [ ] Start Docker Desktop (if not running)
- [ ] Run `docker-compose up --build` to restart containers with new configuration
- [ ] Test access via http://localhost (port 80) and https://localhost (port 443, accept self-signed certificate)

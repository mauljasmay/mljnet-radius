# Fix SNMP Enable Button

## Current Status
- SNMP enable button on admin page doesn't work
- SnmpService only uses config('services.snmp.enabled') instead of database setting

## Plan
- [ ] Update SnmpService constructor to check database setting first
- [ ] Fall back to config if no database setting exists
- [ ] Test the enable button functionality

## Implementation Steps
1. Modify SnmpService::__construct() to load from IntegrationSetting model
2. Ensure backward compatibility with config
3. Verify enable/disable works from admin page

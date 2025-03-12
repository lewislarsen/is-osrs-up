# OSRS Status Checker

A lightweight application built with Slim Framework to monitor OSRS game server status. This application periodically checks the official status page provided by Jagex.

## Requirements

- PHP 8.2 or higher
- Composer

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/lewislarsen/is-osrs-up.git
   cd is-osrs-up
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Create required directories:
   ```bash
   mkdir -p database cache
   ```

4. Set permissions (if needed):
   ```bash
   chmod -R 755 cache database
   ```
   
## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Disclaimer

This application is not affiliated with or endorsed by Jagex Ltd. RuneScape is a registered trademark of Jagex Ltd.
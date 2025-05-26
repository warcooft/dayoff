# 🇮🇩 Indonesian National Dayoffs

Indonesian National Dayoffs is a simple and handy package to help you fetch and display public holiday information in your CodeIgniter 4 application.

![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%5E4.4.0-blue)
![PHP Version Require](https://img.shields.io/badge/PHP-%5E8.0-blue)

## Installation

Use Composer to install the package:

```
composer require aselsan/dayoff dev-develop
```

## Migration

Run the following migration to create the necessary database tables:

```
php spark migrate --all
```

## Fetch Holiday Data

To scrape Indonesian public holiday data, use the custom CLI command:

```
php spark scrap:dayoff --year 2025
```

Or simply run without the --year option to fetch holidays for the current year:

```
php spark scrap:dayoff
```

## Available Methods

```php
<?php

service('dayoff')->now();        // Check if today is a holiday
service('dayoff')->yesterday();  // Get holiday info for yesterday
service('dayoff')->tomorrow();   // Get holiday info for tomorrow
service('dayoff')->thisWeek();   // Get holidays in the current week
service('dayoff')->thisMonth();  // Get holidays in the current month
service('dayoff')->thisYear();   // Get holidays in the current year
```

## Credit

Data is sourced from the awesome web at www.tanggalan.com. Terima kasih!

## License

This project is licensed under the MIT License - see the [LICENSE](/LICENSE) file for details.

# :boom: Rad Tools :sparkles:
A utility belt for Rad developers.
## Installation
Assuming you're working in the project root of a Drupal 8 composer workflow project
1. Add this to your `composer.json`'s `repositories` array:
```json
{
  "url": "https://github.com/radcampaign/rad_tools",
  "type": "git"
}
```
2. Require and install like any other Drupal module.
```sh
$ composer require radcampaign/rad_tools
$ cd web
$ drush en rad_tools
```
## Usage
1. Add `use Drupal\rad_tools\RadTools;` at the top of your file.
2. Go wild (read [`src\RadTools.php`](https://github.com/radcampaign/rad_tools/blob/master/src/RadTools.php) to learn about the functions available.

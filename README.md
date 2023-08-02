# WonderCMS Bootstrap 5 theme
### Author: Joaquim Homrighausen
(https://github.com/joho1968)

Simple Bootstrap 5 theme, inspired by "Simple Material inspired theme" by @tunguskha.

This theme does **not** contain the actual Bootstrap 5 files since I believe in modular design and resource distribution.

The [WCMS-BS5-LOCAL plugin for WonderCMS](https://github.com/joho1968/wcms-bs5-local) plugin comes with Bootstrap 5.x and will serve Bootstrap 5.x files from your web server.

:warning: This theme is not compatible with the SummerNote editor plugin for WonderCMS, since it automatically includes Bootstrap from some CDN.

## Preview
![Theme preview](/preview.jpg)

## How to use
1. Download the wcms-bs5*.zip file
2. Unpack its contents in the `themes/` folder of your WonderCMS site
3. Make sure you remove any folder prefix like `-1.0.0` of the unpacked folder
4. Edit the `data/files/database.js` file and change the theme to `wcms-bs5`

## Dark mode

The theme will, since 1.1.0, automatically detect the visitor browser's "theme
preference" and toggle Bootstrap 5 to the corresponding setting ("dark" or
"light"). One could possibly also add a manual toggle, but this involves a nice
looking icon, and some more code. I would consider adding this if there's a
request for it.

There's, unfortunately, a small "flash effect" when a page is first loaded if
the visitor's browser has "Dark mode" enabled. I'm not sure if there's a better
solution than the one I use in the theme to switch colors, if there is, I'm all
ears.

I'm not entirely satisfied with Bootstrap 5's choices for the background colors
used in its "dark mode", but to stay true to Boostrap's CSS classes, I have
chosen not to modify anything.

## Changelog

### 1.2.1 (2023-08-02)
* Minor fixes/cleanup for search functionality
* Fixes for "navbar" in Dark Mode
* Changed title from h5 to h4 to better line up with menu button and search field
* Added support for searching the Simple Blog plugin "description" field

### 1.2.0 (2023-07-17)
* Menu and site title is now "sticky", they should not scroll with content
* Site title has an HTML TITLE element on it, in those situations where content wraps
* Some additional CSS tweaks are made if the Simple Blog plugin is detected
* Implemented search, it will match the search criteria against page titles and page content. The search function also supports the Simple Blog plugin
* It is now assumed that the mb_ string functions are present since WonderCMS also requires them

### 1.1.1 (2023-06-08)
* Changed footer alignment to centered (instead of right)
* Removed truncation of page title on overflow, it will now wrap instead

### 1.1.0 (2023-06-07)
* Added support for "Dark mode" (auto detected)

## License

Please see [LICENSE](LICENSE) for a full copy of GPLv2

Copyright (C) 2023 [Joaquim Homrighausen](https://github.com/joho1968); all rights reserved.

This file is part of wcms-bs5. wcms-bs5 is free software.

You may redistribute it and/or modify it under the terms of the GNU General
Public License version 2, as published by the Free Software Foundation.

wcms-bs5 is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
the wcms-bs5-local package. If not, write to:

```
The Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor
Boston, MA  02110-1301, USA.
```

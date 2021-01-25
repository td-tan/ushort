# ushort
My simple frameworkless URL shortener in PHP with vanilla JS + a sprinkle $ jquery for SPA

# How to setup

1. Install required php packages with composer (composer.json)
2. Configure environment variables in .env and in phinx.php
3. Compose up with docker-compose.yml
4. Migrate db with phinx
5. Set webserver in public dir

# Demo

## Let's log into it:
![Login](demo/ushort-demo0.gif)

## Edit a short link and save it:
![Edit-Save-Try short link](demo/ushort-demo1.gif)

## Have fun creating new short links:
![Create new short link](demo/ushort-demo3.gif)

## Or delete them (soft delete):
![Delete short link](demo/ushort-demo4.gif)

# LICENSE
MIT License

Copyright (c) 2021 td-tan

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

# Naturalis timeviewbox downloader

Usage
---
```
php downloader.php ARGS
```


-h           : Display help screen

-n %NAME%    : Give the project name can be found in the URL
               http://example.timeboxview.com, in which case
               example would be the project name.
               
-i %MINUTES% : Interval in minutes inbetween downloaded images.
               Minimum is 5 minutes, and all intervals will be
               rounded to the nearest division of 5 as the
               service only provides an image for every 5 minutes.
               Note images will download as fast as possible this
               is the interval of time inbetween when the images
               were taken!
               
-t %OPTION%  : Choose if you want to download thumbnails or
               full size images. (thumb for thumbnails and full
               for full size images). If this is not set full is
               default

What is this for?
---
This script was created as a tool to automatically download one or multiple images for each day. This is planned to be used to create a timelaps video. It might be useful for other things too. This code works on all public TimeWriters timeviewbox projects. It was originally created to create a timelaps video for the Naturalis Building project.



For available public projects: https://www.thetimewriters.com/lopende-projecten/




This project is in no way associated with either TimeWriters or Naturalis.

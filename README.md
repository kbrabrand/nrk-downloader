# nrk-downloader
Stream downloader for NRK depending on `PHP`, `bash` and `aconv`/`ffmpeg`. Will download subtitles if found.

## Installation
Install `ffmpeg`. If you're on a mac, you'll need to install [Homebrew](http://brew.sh/) first, and then install ffmpeg (shown below). If you're on some linux distribution you hopefully know what to do. If you're on Windows I'm sorry..

```sh
$ brew install ffmpeg
```

Then `git clone` or download a [zip file](https://github.com/kbrabrand/nrk-downloader/archive/master.zip) of this repo.

Navigate to the folder you've cloned/downloaded and make the `nrk-downloader` file executable and symlink it into your bin folder:

```sh
$ chmod +x nrk-downloader
$ sudo ln -s [full path to nrk-downloader] /usr/local/bin/nrk-downloader
```

## Usage
Browse to the page of the episode/program you want to download on tv.nrk.no and copy the link (including leading http://);

```bash
$ nrk-downloader http://tv.nrk.no/serie/kveldsnytt/NNFA23020515/05-02-2015
```

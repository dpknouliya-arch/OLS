## Office Web Apps Viewer 
('.ppt' '.pptx' '.doc', '.docx', '.xls', '.xlsx')

    http://view.officeapps.live.com/op/view.aspx?src=[OFFICE_FILE_URL]

    <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=[OFFICE_FILE_URL]' width='px' height='px' frameborder='0'>
    </iframe>

### OneDrive Embed Links

**Powerpoint**

```
<iframe src="https://onedrive.live.com/embed?cid=CA582F2BC3AD1590&resid=CA582F2BC3AD1590%2133463&authkey=AFh1O3tkDLAQzzw&em=2" width="402" height="327" frameborder="0" scrolling="no"></iframe>
```

**Excel**

```
<iframe src="https://onedrive.live.com/embed?cid=CA582F2BC3AD1590&resid=CA582F2BC3AD1590%2136277&authkey=AHJ96YRJIbbJcec&em=2" frameborder="0" scrolling="no" width="800" height="346"></iframe>
```

## Google Docs Viewer

Only files under 25 MB can be previewed with the Google Drive viewer.

Google Drive viewer helps you preview over 16 different file types, listed below:

* Image files (.JPEG, .PNG, .GIF, .TIFF, .BMP)
* Video files (WebM, .MPEG4, .3GPP, .MOV, .AVI, .MPEGPS, .WMV, .FLV)
* Text files (.TXT)
* Markup/Code (.CSS, .HTML, .PHP, .C, .CPP, .H, .HPP, .JS)
* Microsoft Word (.DOC and .DOCX)
* Microsoft Excel (.XLS and .XLSX)
* Microsoft PowerPoint (.PPT and .PPTX)
* Adobe Portable Document Format (.PDF)
* Apple Pages (.PAGES)
* Adobe Illustrator (.AI)
* Adobe Photoshop (.PSD)
* Tagged Image File Format (.TIFF)
* Autodesk AutoCad (.DXF)
* Scalable Vector Graphics (.SVG)
* PostScript (.EPS, .PS)
* TrueType (.TTF)
* XML Paper Specification (.XPS)
* Archive file types (.ZIP and .RAR)

### Google Docs Viewer (Apps)
    https://docs.google.com/a/[DOMINIO]/viewer?url=[FILE_URL]
  
### Google Docs Viewer
    https://docs.google.com/a/[DOMINIO]/viewer?url=[FILE_URL]

## Google Drive

**Sheets**

```
<iframe src="https://docs.google.com/spreadsheets/d/13R8O15c_sZKZT2QRHom1z2SDA3E1O5chUvROnqHCkwE/pubhtml?widget=true&amp;headers=true" style="width:100%;height:100%;"></iframe>
```

### Embedded File Viewer

Google Docs offers an undocumented feature that lets you embed PDF files and PowerPoint presentations in a web page. The files don't have to be uploaded to Google Docs, but they need to be available online.

**Google Drive Viewer: Explicit PDF files**
```
<iframe src="https://docs.google.com/viewer?url=http://infolab.stanford.edu/pub/papers/google.pdf&embedded=true" style="width:600px; height:500px;" frameborder="0"></iframe>
```

```
<iframe src="https://drive.google.com/viewerng/viewer?url=http://docs.google.com/fileview?id=0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh&hl=en&pid=explorer&efh=false&a=v&chrome=false&embedded=true" frameborder="0"></iframe>
```

**Google Drive Viewer: Non-PDF files (fileviewer URL)**

To view Google Drive docs from fileviewer links, use the file ID as the `srcid` attribute in the iframe.

The file ID for your PDF (one that is already in Google Drive) can be found in the PDFs web address. When you open a PDF, it’s the garbage-looking piece of the URL (it will be between forward-slashes, “/”).

![](http://www.benschersten.com/blog/wp-content/uploads/2014/04/Screen_Shot_2014-04-30_at_8_29_17_AM.jpg)

The file ID is highlighted in yellow.

In this case it’s the 0B3xoQi_oa7_hU2J5S1RQbFdqS3c

Source: [http://docs.google.com/fileview?id=0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh&hl=en&pid=explorer&efh=false&a=v&chrome=false&embedded=true](http://docs.google.com/fileview?id=0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh&hl=en&pid=explorer&efh=false&a=v&chrome=false&embedded=true)

`id: 0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh`

Or

Source: [https://drive.google.com/file/d/0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh/view?ddrp=1&hl=en#](https://drive.google.com/file/d/0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh/view?ddrp=1&hl=en#)

`id: 0B5ImRpiNhCfGZDVhMGEyYmUtZTdmMy00YWEyLWEyMTQtN2E2YzM3MDg3MTZh`

Result:

```
<iframe src="https://docs.google.com/viewer?srcid=[put your file id here]&pid=explorer&efh=false&a=v&chrome=false&embedded=true" width="580px" height="480px"></iframe>
```

## Resources

  
Info:
http://www.labnol.org/internet/google-docs-viewer-alternative/


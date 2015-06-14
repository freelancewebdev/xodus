--------------------
Extra: Xodus
--------------------
Version: 1.0 -
 
Xodus is a simple MODX Revolution extra to enable the export of users from chosen user groups to CSV or Excel file format.

Xodus was coded by Joe Molloy <info@hyper-typer.com> and first released in November 2012.

Xodus makes use of a modified version of PHPExcel (http://phpexcel.codeplex.com/).  The modification just changes the location of the temporary folder used by the library so that it works without issue on shared hosting systems with open_basedir restrictions which exclude use of the system temporary folder.  I have also removed the PDF folder from the shared folder in the library to reduce the size of the package - the fonts in the PDF library were adding over 17MB to the overall size of the package.

You can find more information on Xodus in the blog post dedicated to it at http://www.hyper-typer.com/news/xodus-export-modx-users.

Translation Credits:
Dutch: 		Mark Hamstra  
Spanish:  	Cecilia Marnero  
Russian: 		Tatyana Grishna  
German: 		Markus Gottschau  





Site archive for the Communicating with Prisoners ortext (acrosswalls.org). Version 1.0

* Freedom for use *

The site content and data arrangements were authored by the Communicating with Prisoners Collective (CWPC).
CWPC releases its work (writing content and code, arranging data) under a Creative Common CCO "No Rights Reserved" Declaration. See http://creativecommons.org/about/cc0

WordPress Ortext plugins and themes in this archive incorporate some GNU General Public License code. Hence those plugins and theme are free software under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. The GNU General Public License is available at 
http://www.gnu.org/licenses/gpl-2.0.html
Alternatively, write write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

This site archive is distributed in the hope that its contents will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 

Use the contents of this site archive for good, in the public interest.

* Archive description *
* Guide for replication and experimentation with this ortext *
* Using the XML Export File to Replicate This Site *
* Using the XML Export File to Replicate This Site *

The most update instructions documentation is available from: 
http://acrosswalls.org/about/
---------------------------------

* Archive description *

This dataset is a zipped content and code archive for this site. After downloading it, you first need to unzip it to find the contents listed below. You can usually unzip the file by double-clicking on it to start the unzip process.

The archive contains some content redundancy. The redundancy expands options for addressing technical challenges. The redundancy also provides flexibility in viewing and using the content.

Note that internal links within the site’s content point have the URL base localhost/wordpress  So do the source URL’s for images. If you want to replicate this site on your own server at a different address, you have to re-write the base for the internal links.

Dataset contents:

1.    Communicating with Prisoners WordPress database as a SQL archive file. This file provides all the WordPress content and the theme setup. It’s a gzipped file of SQL statements for recreating the Communicating with Prisoners content tables. Importing the SQL file directly into the WordPress installation database is the simplest way to replicating this site. The SQL database archive file is named backup_{date}_communicating_with_prisoners_{id string}-db.gz
    Note: do not share publicly an archive of your WordPress site.  We have carefully crafted the above archive so as to address the security issues involved in sharing such an archive.

2.    Communicating with Prisoners content as a WordPress XML export file. The WordPress XML export file was created using the native WordPress export function to export all site content. The WordPress XML export file doesn’t include the site’s graphs and images. Those are in the uploads folder. It also doesn’t include the tables. Those have to be imported separately. The XML export file is named communicatingwithprisoners.wordpress.{date}.xml
 
3.   TablePress export file. TablePress tables exported with the native TablePress functionality. The export format is JSON, one file per table, compressed into a standard zip file. The TablePress export file is named tablepress-export-{data and time}-json.zip.

4.    TablePress custom css file. For the Communicating with Prisoners ortext, the contents of this file belongs in the custom css box at TablePress -> Plugin Options. The file is tablespress-custom-css.txt

5.    Zipped WordPress uploads folder. This folder (unzipped) is in wp-content in a standard WordPress installation.  For this ortext, the uploads folder contains WordPress natively uploaded media (graphs and photos). It also contains the data and faces folders. The zipped uploads folder is named backup_{date}_communicating_prisoners_{id string}-uploads.zip

6.   The data folder. The data folder contains spreadsheet datasets used in this ortext in Excel and OpenOffice file formats in subfolders excel and openoffice, respectively. This folder includes manifest.csv  which describes hosting urls for dataset versions. You can edit this file and re-import it to change the hosting of datasets. A convenient way to modify manifest.csv is via the manifest-admin workbook. The data folder is within the zipped uploads folder.

7.    The faces folder. The faces folder contains faces of prisoners displayed randomly within the Communicating with Prisoners ortext. These images, which were collected from public postings on the web, are directly relevant to communicating with prisoners. Do not use these images inappropriately. The faces folder is within the zipped uploads folder.

8.    Zipped ortext theme folder. Contains the custom Ortext theme (ortext folder) for presenting this ortext. It is in the file named backup_{date}_communicating_prisoners_{id string}-themes.zip

9.    Zipped WordPress plugins folder. This folder includes the Ortext Formatting plugin (otx-format folder), the Ortext Datalinks plugin (otx-datalinks folder), The plugins folder also includes the excellent TablePress plugin (tablepress folder) available under a GPL license. These are plugins you need to upload and activate to view the Communicating with Prisoners ortext. The zipped plugins folder is named backup_{date}_communicating_prisoners_{id string}-plugins.zip

10.    Ortext BibTex Importer plugin. Unless you’re importing BibTex references, you don’t need this plugin. Good security practice is not to have on a public server a plugin that you’re not using. The zipped plugin file is otx-bibtex-importer.zip

11.    Zipped Ortext Build plugin. The Ortext Build plugin is used for custom timelining and internal key-linking in building an ortext. It also provides a framework for running utility functions, including flipping between key links and full-url permalinks. Because the operations the plugins performs are relatively vulnerable to security exploits, don’t move this plugin to a public accessible webserver unless you really know what you’re doing. The zipped plugin file is otx-build.zip

12.    BibTex reference file. The Ortext BibTex Importer plugin imported the references into the Communicating with Prisoners ortext using files like this one. The references are already included in the ortext SQL database and XML export files. The BibTex reference file gives you an alternative source for working with the references. It was created as an EndNote export file. The BibTex reference file is named references.txt


----------------------------------

* Guide for replication and experimentation with this ortext *


A good first step toward experimenting with the Communicating with Prisoners ortext is replicating it. The site archive includes all you need to replicate this site. It includes the site content in redundant forms to increase your flexibility in working with it.

To work with this site, we suggests that you start with a new installation of WordPress. You could import the Communicating with Prisoners content as additional content to an existing WordPress site, but that’s technically difficult to do well. You can get a new installation of WordPress through a commercial online hosting service. Alternatively, you can install WordPress on your local computer and experiment with this ortext locally. XAMPP provides a simple, free local server for installing WordPress and other similar software.

Setting up a local replica of this site based at localhost/wordpress is very easy. Establish a new WordPress installation on your computer at that address. For security, ensure that your local installation is blocked from public Internet access. Then within the mySQL database supporting your new local WordPress installation, delete all the tables. Then import into that database the site archive database backup_{date-time}_Communicating_with_Prisoners_{number/letter string}-db.gz Unzip the site archive folders plugins, themes, and uploads and use them to replace the corresponding folders in your installation’s wp-content folder. You can then login to your local replica of the site with username prisoners333 and password compassion For an additional layer of security, you should immediately change the site password. You should also change the email address on the admin Settings page and for the username prisoners.

This site’s archive includes all the content exported with the native WordPress Export tool. You can use that XML export file to replicate this site anywhere using the WordPress Importer. That approach doesn’t require direct manipulation of the underlying WordPress mySQL database. No WordPress technical expert would ordinarily use the XML export file to replicate a site. But doing so can provide a technical learning experience. It also will provide, even if merely done reasonably correctly, an installation sufficient for experimenting with this ortext.

At low cost, you can replicate this site anywhere without having any technical knowledge. One simple approach is to purchase the UpdraftPlus Migrator addon for $30. That software provides a simple, drag-and-drop means to replace an existing WordPress installation with a replica of this site. Other similar commercial software probably also exists.

A person proficient in WordPress installations and migrations can easily replicate this site anywhere from the site archive files without purchasing any software. The underlying standard technical approach involves a PHP serialized-data search-and-replace for re-basing the internal links, a mySQL import, and copying folders into your WordPress installation’s wp-content directory. Most commercial online hosting providers offer such migration service for free with a new hosting account. Especially if done for an online server, the migration process can be difficult and dangerous for a person not knowledgeable about standard web programming tools.

An important aspect of this ortext is integration with externally hosted datasets. The archive folder data includes Excel and OpenOffice versions of the datasets. To have datasets hosted at locations of your choosing, you need to update the datalinks manifest.

The replication process doesn’t include installing the Ortext BibTex Importerplugin or the Ortext Build plugin. You don’t need the Ortext BibTex Importer and Ortext Build plugins to replicate the Communicating with Prisoners site. You can do a lot of experiments and changes to without those two plugins. Not installing on a public server plugins that you’re not using is good security practice. We see little risk in using the Ortext BibTex Importer for importing references on a public site. Good security practice in any case would be to delete the plugin from the public site after you’re finished using it. The Ortext Build plugin involves more risky code. We don’t recommend including it on a publicly accessible site.

This ortext’s authors have released their work under a CCO 1.0 Universal Public Domain Dedication. Replicating this ortext is only a first step. The next steps are for you to make something better.

This site provides seeds for ortexts. It’s not a project trunk. We have not developed a central collaborative platform for revising and improving this ortext. We encourage others to explore such possibilities.

-------------------------

* Using the XML Export File to Replicate This Site *

Although there are simpler, better ways to replicate the Communicating with Prisoners ortext, you can get the job done using the XML export file and other content files from the site source archive.

Here are steps to create a working replica of this site using the XML file:

1.    Download the Communicating with Prisoners ortext source archive. This will be called the source. See the download page for details on where to find specific items within the source.

2.    Install WordPress on your web server.

 3.   Within the WordPress admin Settings -> Permalinks, change the permalink form by setting the Post name button. Set the Category Base to section and the Tag base to topic

4.    Set up the media. Within the WordPress admin Settings -> Media, change the medium size to 550×550 and uncheck Organize my uploads into month- and year-based folders

5.    Replace the uploads folder. Use standard tools to replace your WordPress installations uploads folder with the source uploads folder. For an online server, you upload a folder to WordPress installation independently of WordPress using a ftp or sftp (better) program. If you don’t do that, you won’t see the faces of the prisoners and you won’t see any graphs or images within the content.

6.    Add the Ortext theme to your installation. If you zip the relevant source ortext theme folder, you can upload it within WordPress admin via Appearance -> Themes, click on Add New button, and then click on Upload button. Alternatively, you need to transfer the folder to your wp-content/themes directory using standard tools. Once you have moved the Ortext theme (ortext theme folder) into your WordPress installation, activate the Ortext theme.

7.    Add two Ortext plugins and TablePress to your installation. The two ortext plugins are Ortext Formating and Ortext Datalinks. If you zip the relevant source plugin folders otx-format and otx-datalinks, you can upload them within WordPress admin via Plugins -> Add New, and click on the Upload Plugin button. Alternatively, you need to move those plugin folders to your wp-content/plugins directory using standard tools. TablePress can be installed similarly, or from the WordPress plugin directory. Once you have installed the three plugins Ortext Formating, Ortext Datalinks, and TablePress, activate them.

8.    Rebase the hyperlinks if necessary. The Communicating with Prisoners XML content file communicatingwithprisoners.wordpress.{date}.xml has internal links with the base localhost/wordpress.  If that’s not your host URL, you need to re-based the internal links. That means changing all instances of localhost/wordpress (search) to example.com (replace), where you substitute your base URL for example.com. You can do this with a search-and-replace on the xml content using a simple code editor such as Notepad++.  Don’t use a word processor such as Microsoft Word. If you have installed your server locally at localhost/wordpress, you don’t have to do these search-and-replaces because the search and replace strings are identical.
  
9.  Import ortext XML content. Make sure first that you have the Ortext plugins activated so that you see Notes, Statistics, Datasets, etc. in the left menu. Within WordPress admin Tools -> Import, click on WordPress. You will be prompted to install the WordPress Importer plugin. Alternatively, you may have to install it from the WordPress directory at Plugins -> Add New. Activate it and go back to Tools -> Import as above. When you get to the WordPress Importer import screen, browse for the site archive file communicatingwithprisoners.wordpress.{date}.xml That’s the one to import. Click Upload File and Import. On the subsequent screen, assign all post to the user name you created at installation (do that through the drop-down box). DO NOT check the Importer’s checkbox for Download and import file attachments.  Perform the content import. It could take 5 minutes or more to complete. You will see a completion note appear on the screen. The media imports will fail. Don’t worry about that.

10.    Customize the Ortext theme. Make sure it’s active. Go to WordPress admin Appearance -> Customize.
        Expand the Navigation customization. Set the Primary Menu as left nav and the Secondary Menu as right nav.
        Expand Widgets -> Footer Widget. Open each of the footer widget items and remove them.
        Expand the Static Front Page customization and set Static Page. Then choose for the front page Front Page: Communicating with Prisoners.
Click on Save & Publish at the top of the column. Exit via the top left X.

11.    Import the TablePress tables and css.  Go to TablePress -> Import a Table. Browse for the import file tablepress-export-{data and time}-json.zip Set the import format to JSON. Press the Import button to import the tables. Then at TablePress -> Plugin Options, copy and paste the css contents of the source file tablepress-custom-css.txt into the relevant box. Press the Save Changes button.

If you have successfully completed all these steps, you’re good. Even if you have messed up some step, you should still have some content for ortext experimentation.

Unfortunately, the hyperlinks in the TablePress files are probably wrong. They point to http://localhost/wordpress/…. A search-and-replace is burdensome because the TablePress tables are archived in JSON (which has a backslash before the forward slash) and each table is in a separate fie. So it’s easiest simply to edit manually the hyper links for each table (usually only one hyperlink per table) through the TablePress editor in the WordPress admin. Alternatively, just don’t worry about the problem of non-functioning source links in the TablePress tables.

Unless you’ve installed locally at localhost/wordpress, the source media won’t appear in the admin Media panel. That’s because the attachments aren’t set up correctly. Don’t worry about that for an experimental site. Even though it doesn’t appear in the media admin panel, all the media will appear correctly on the site’s front end. If you want to modify any of the media and use it elsewhere in the content, re-upload it within WordPress via the standard WordPress insert media process. You can also do that to recreate the media in the media folder if you are unable simply to move the media there.


------------------------------

* Using UpdraftPlus Migrator to Replicate This Site *

A simple way to replicate this site anywhere is with the UpdraftPlus Migrator addon. That’s commercial software that costs $30. You don’t need to purchase that software to replicate this site very easily on a WordPress local (on your own computer) installation based at localhost/wordpress  Replicating this site at other local or online addresses is more complicated, but can be done at no cost.

UpdraftPlus Migrator transforms an existing WordPress installation into a replica (clone) of this site via steps using terms like restore and backup.  Don’t be confused. Following the steps on this page will cause any content you have in the existing WordPress installation to vanish. Its content will be replaced by a replica of the Communicating with Prisoners site. That’s why you should start with a new WordPress installation when replicating this site. You can establish one on your computer at no cost or purchase online hosting through a commercial hosting provider. Before proceeding, make sure that you understand that whatever you have in the existing WordPress installation will be lost by following the procedure below.

To avoid a serious security risk after replicating this site, IMMEDIATELY CHANGE THE WORDPRESS LOGIN PASSWORD. That’s absolutely necessary if you are replicating this site on a publicly accessible WordPress installation. You should also change the email addresses on the admin Settings page and for the username prisoners.

Changing the username in the database archive file before importing is good security practice. The username and password for the WordPress installation in the database archive file are prisoners333 and compassion, respectively. On a public installation, a hacker might get into your installation via the above public credentials between the very short time between the migration and your changing the password. That’s probably a very small risk. To eliminate it, you can change the username prisoners333 in the database archive file. Use the search functionality in a text editor (such as Notepad++) to perform that task. Making such a change is generally a good security practice. Having a non-public username makes it harder for someone to crack your site’s login. They need to figure out both your username and your password.

To replicate this site using the UpdraftPlus Migrator addon, buy that software for $30. Its provider gives instructions on how to use Migrator to migrate a site. Here are additional, more detailed instructions.

Once you have the Migrator addon installed in your WordPress installation, from the WordPress admin go to Settings ->UpdraftPlus Backups. At that page click on Restore. On the Restore page, click Upload backup files (adjacent to More tasks:). Into the space that opens, drag one at a time from the unzipped Communicating with Prisoners site archive the following files:

    backup_{date-time}_Communicating_with_Prisoners_{number/letter string}-db.gz
    backup_{date-time}_Communicating_with_Prisoners_{number/letter string}-plugins.zip
    backup_{date-time}_Communicating_with_Prisoners_{number/letter string}-themes.zip
    backup_{date-time}_Communicating_with_Prisoners_{number/letter string}-uploads.zip

Each file will be added to a single line below corresponding to the (common) date-time in the backup file names. After you have added the above four files, click on the Restore button to the right of the date-time line showing the labels Database Plugins Themes Uploads. A panel will pop up.  Check the boxes for restoring Database, Plugins, Themes, and Uploads. You must also check the box for search and replace site location in the database. Then click the Restore button on the popup panel (it’s to the left of the Cancel button).  The Migrator will then run and replace your WordPress installation with a replica of this site. It may take a few minutes to run.

Immediately after the Migrator finishes running, log out of your WordPress installation (you’ll be forced out when you click on anything).  Then immediately log back in. To log back in, you have to use the username prisoners333 and the password compassion. Immediately change the login password to a strong password. You should also change the email address on the admin Settings page and for the username prisoners. Then you are set to experiment with your own copy of the Communicating with Prisoners ortext.

The database file re-creates tables with the WordPress table prefix wp_.  That’s the usual table prefix in a standard WordPress installation. If your WordPress installation uses a different table prefix, the site replication will fail. You have to figure out a way to transform the table prefixes. That’s not difficult for someone knowledgeable about web programming tools.

You are free to modify, adapt, and improve the Communicating with Prisoners ortext. You can host the datasets on your own site by changing the datalinks. If you want to improve the Communicating with Prisoners ortext rather than just experiment with an ortext, look for a good online collaborative platform for doing so.

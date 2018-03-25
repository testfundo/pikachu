{$header}
<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData' style='text-align: justify;'>

			<h2 style='text-align: center;'>DOWNLOAD</h2>

			<div class='standardMargins'>
				The software that runs <a href="about">NutriDB</a> is released under an <a href="http://www.opensource.org/licenses/mit-license.php">MIT (X11)</a> license.
			</div>

			<div class='standardMargins'>
				A tar-gzipped version is available here:
			</div>

			<div style='margin-left: 2em;'>
				<div>
					<a href="http://nutridb.org/nutridb.tar.gz">nutridb.tar.gz</a>
				</div>
			</div>
			<div class='standardMargins'>
				The source is also available in <a href="http://git-scm.com/">git</a>.  You can
				<a href="https://natha.nkinka.de/code/git/nutridb.git">browse the repository</a>, or make
				read-only clones:
			</div>
			
			<div style='margin-left: 2em;'>
				<div>
					<code>git://natha.nkinka.de/nutridb.git</code>
				</div>
			</div>


			<div class='standardMargins'>
				The MySQL database is included inside the tar-gzipped file, but can also be
				downloaded individually: <a href="nutridb-database-sr28.sql.gz">nutridb-database-sr28.sql.gz</a>
			</div>

			<div class='standardMargins'>
				<strong>Installation</strong>: First take a look at the README file that comes with
				the source.  To get a site up and running should be as simple
				as cloning the git repository, or unpacking one of the
				tar-gzipped files, into your web root, copy config.php-dist to
				config.php, edit config.php with some values suitable for your
				environment, unzip the database and install it with something
				like <code>mysql -u root -p &lt; nutridb-database.sql</code>.
			</div>

		</div>
	</div>

	<div id='leftColumn'>
		<div id='leftData'>
			{$sidebar_left}
		</div>
	</div>

	<div id='rightColumn'>
		<div id='rightData'>
			{$sidebar_right}
		</div>
	</div>

</div>
{$footer} 

<?php

	/**
	*	@brief API for Audirvana database handling
	*	@author Aleksi Räsänen <aleksi.rasanen@runosydan.net>
	**/
	class CAudirvanaDatabaseAPI
	{
		private $db;
		private $db_file = 'AudirvanaPlusLibrary.sqlite';	
		private $covers_path = 'covers/';
		private $original_path = 'Users/stargazers/Music/';
		private $new_path = 'http://media.korpilaakso.net/'; 
		private $covers_generated = false;

		/**
		*	@brief Initializes SQLite3 instance
		*/
		public function __construct()
		{
			$this->db = new SQLite3( $this->db_file );
		}

		/**
		*	@brief Generates covers to covers arts folder
		*		   if file already does not exists.
		*/
		public function generateCoverArts()
		{
			if(! file_exists( $this->covers_path ) )
				mkdir( $this->covers_path, 0755 );
			
			$q = 'SELECT ZARTWORKVIGNETTE, ZALBUMARTISTSNAMES, '
				. 'ZTITLE FROM ZALBUM';
			$ret = $this->db->query( $q );

			while( $row = $ret->fetchArray())
			{
				$filename = $this->getAlbumCoverFilename( $row );
				$filename = $this->covers_path . strtolower( $filename );

				if(! file_exists( $filename ) )
				{
					$fh = fopen( $filename, 'w' );
					fwrite( $fh, $row['ZARTWORKVIGNETTE'] );
					fclose( $fh );
				}
			}

		}

		/**
		*	@brief Generate album cover filename
		*	@param $row Array where must be keys ZALBUMARTISTSNAMES and ZTITLE
		*	@return String
		*/
		public function getAlbumCoverFilename( $row )
		{
			$filename = $row['ZALBUMARTISTSNAMES'] . '-' . $row['ZTITLE'];
			$filename = str_replace( ' ', '_', $filename );
			$filename = str_replace( ':', '_', $filename );
			$filename = str_replace( '/', '_', $filename );
			$filename = str_replace( '(', '_', $filename );
			$filename = str_replace( ')', '_', $filename );
			$filename = str_replace( '!', '_', $filename );
			$filename = str_replace( '&', '_', $filename );
			$filename = str_replace( 'ä', 'a', $filename );
			$filename = str_replace( 'ö', 'o', $filename );
			$filename = str_replace( 'å', 'a', $filename );
			$filename = str_replace( 'â', 'a', $filename );
			$filename = str_replace( '?', '_', $filename );
			$filename = str_replace( '\'', '_', $filename );
			$filename .= '.jpg';
			$filename = strtolower( $filename );

			// Make sure we do not have files which starts with -,
			// because it is annoying to remove them on shell.
			if( substr( $filename, 0, 1 ) == '-' )
				$filename = 'unknown_artist' . $filename;

			return $filename;
		}

		/**
		*	@brief Get tracks for album
		*	@param $album_id Album ID number
		*	@return Array of tracks
		*/
		public function getTracksForAlbum( $album_id )
		{
			// Get tracks for this album
			$q = 'SELECT ZTRACKNUMBER, ZPLAYCOUNT, ZTITLE, ZLOCATIONRELPATH '
				. 'FROM ZTRACK WHERE ZALBUM="' . $album_id. '"'
				. 'ORDER BY ZTRACKNUMBER';

			$track_ret = $this->db->query( $q );
			$tracks = '';

			// Add tracks array to this row
			while( $track_row = $track_ret->fetchArray())
			{
				// Where we can find these files on server? 
				// Change path to URL/another path.
				$file_url = str_replace( $this->original_path, 
					$this->new_path, $track_row['ZLOCATIONRELPATH'] );

				$track['number'] = $track_row['ZTRACKNUMBER'];
				$track['title'] = $track_row['ZTITLE'];
				$track['playcount'] = $track_row['ZPLAYCOUNT'];
				$track['url'] = $file_url;

				$tracks[] = $track;
			}

			return $tracks;
		}

		/**
		*	@brief Get album informations in JSON format
		*   @return JSON String
		*/
		public function getData()
		{
			$q = 'SELECT Z_PK, ZALBUMARTISTSNAMES, ZTITLE FROM ZALBUM';
			$ret = $this->db->query( $q );

			while( $row = $ret->fetchArray())
			{
				$tracks = $this->getTracksForAlbum( $row['Z_PK'] );
				$filename = $this->getAlbumCoverFilename( $row );
				$row['cover'] = $this->covers_path . $filename;
				$row['tracks'] = $tracks; 
				$rows[] = $row;

				// Generate cover art if required (and run this only once!)
				if(! file_exists( $row['cover'] ) && 
					! $this->covers_generated )
				{
					$this->generateCoverArts();
					$this->covers_generated = true;
				}
			}

			return json_encode( $rows );
		}
	}

	$api = new CAudirvanaDatabaseAPI();
	echo $api->getData();

?>

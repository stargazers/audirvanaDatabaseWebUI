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

		/**
		*	@brief Initializes SQLite3 instance
		*/
		public function __construct()
		{
			$this->db = new SQLite3( $this->db_file );
		}


		/**
		*	@brief Generate album cover filename
		*	@param $row Array where must be keys ZALBUMARTISTSNAMES and ZTITLE
		*	@return String
		*/
		public function get_album_cover_filename( $row )
		{
			$filename = $row['ZALBUMARTISTSNAMES'] . '-' . $row['ZTITLE'];
			$filename = str_replace( ' ', '_', $filename );
			$filename = str_replace( '/', '_', $filename );
			$filename = str_replace( 'ä', 'a', $filename );
			$filename = str_replace( 'ö', 'o', $filename );
			$filename = str_replace( '?', '_', $filename );
			$filename .= '.jpg';
			$filename = strtolower( $filename );

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

				$filename = $this->get_album_cover_filename( $row );
					
				$row['cover'] = $this->covers_path . $filename;
				$row['tracks'] = $tracks; 
				
				$rows[] = $row;
			}

			return json_encode( $rows );
		}
	}


	$api = new CAudirvanaDatabaseAPI();
	echo $api->getData();

?>

<?php

	/*
	 * file: class.software.php
	 * author: adrien waksberg
	 * mail: a.waksberg[at]yaegashi.fr
	 * info: a class for check the new software version
	 */

	class Software {

		const NO_ERROR = 0;
		const ERROR_NO_MATCH = 1;
		
		private $id;
		private $name;
		private $category;
		private $url;
		private $url_regex;
		private $date;
		private $version;
		private $preview_date;
		private $preview_version;
		private $regex;
		private $error;
		private $sql;
		
		public function __construct($sql, $id = null) {
			$this->id = $id;
			$this->sql = $sql;

			if (!is_null($this->id)) {
				$this->refresh();
			}
		}


		// Set the error indicator
		// @rtrn: false if there is an error
		private function close() {
			$query = $this->sql->prepare('UPDATE version
			                              SET error=?
			                              WHERE id=?
			                              LIMIT 1;');
			$query->bind_param('ii', $this->error, $this->id);
			$return = $query->execute();
			$query->free_result();

			return $return;
		}

		// check the software version
		// @rtrn: true if new version is checked
		public function checkVersion() {
			$data = $this->checkUrl();
			if (preg_match("#$this->regex#", $data, $version)) {
				$version = $version[1];
				if ($version != $this->version) {
					if ($this->setVersion($version)) {
						$this->error = self::NO_ERROR;
						return true;
					}
				}
			} else {
				$this->error = self::ERROR_NO_MATCH;
			}

			return false;
		
		}

		// check url and download html page
		// @rtrn: the text/html page
		public function checkUrl() {
			$curl = curl_init($this->url_regex);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			
			return curl_exec($curl);
		}

		// refresh the software information
		// @rtrn: false if one error
		private function refresh() {
			$query = $this->sql->prepare('SELECT name, category, url, url_regex, regex, error, number, date 
			                              FROM software AS s 
			                              INNER JOIN version AS v
			                              ON s.id = v.software_id
			                              WHERE s.id=?
			                              ORDER BY v.date DESC
			                              LIMIT 1;');
			$query->bind_param('i', $this->id);
			
			if ($query->execute()) {
				$query->bind_result($name, $category, $url, $url_regex, $regex, $error, $version, $date);
				$query->fetch();
				$query->free_result();
				
				$this->name = $name;
				$this->category = $category;
				$this->url = $url;
				$this->url_regex = $url_regex;
				$this->regex = $regex;
				$this->error = $error;
				$this->version = $version;
				$this->date = $date;

				return true;
			}

			return false;
		}

		
		// Add a new software in database
		// @args: $name -> the software name
		//        $category -> the software category
		//        $url -> the official website
		//        $url_regex -> the url to check version
		//        $regex -> the regex to find the version
		// @rtrn: false if there is one error
		public function add($name, $category, $url, $url_regex, $regex) {
			$query = $this->sql->prepare('INSERT INTO software(name, category, url, url_regex, regex) 
			                              VALUES(? , ? , ? , ?, ?);');
			$query->bind_param('sssss', $name, $category, $url, $url_regex, $regex);
			if ($query->execute()) {
				$query->free_result();
				
				// On récupére l'id du software
				$query = $this->sql->prepare('SELECT id 
				                              FROM software 
				                              WHERE name=? 
				                              LIMIT 1;');
				$query->bind_param('s', $name);
				$query->execute();
				$query->bind_result($id);
				$query->fetch();
				$query->free_result();
				
				$this->name = $name;
				$this->url = $url;
				$this->url_regex = $url_regex;
				$this->regex = $regex;
				$this->error = self::NO_ERROR;
				$this->version = 'N/A';
				$this->date = time();

				// On insert une ligne de version nulle
				$this->id = $id;
				return $this->setVersion('N/A');
			
			}

			return false;

		}

		// Update a software
		// @args: $name -> the new software name
		//        $category -> the new category
		//        $url -> the new official website url
		//        $url_regex -> new check url
		//        $regex -> the new regex to find version
		//@rtrn: false if there is an error
		public function update($name, $category, $url, $url_regex, $regex) {
			$query = $this->sql->prepare('UPDATE software
			                              SET name=? , category=? , url=? , url_regex=? , regex=?
			                              WHERE id=?
			                              LIMIT 1;');
			$query->bind_param('sssssi', $name, $cateogry, $url, $url_regex, $regex, $this->id);
			$return = $query->execute();
			$query->free_result();

			if ($return) {
				$this->name = $name;
				$this->category = $category;
				$this->url = $url;
				$this->url_regex = $url_regex;
				$this->regex = $regex;
			}

			return $return;
		}

		// Search the preview version
		private function previewVersion() {

			if (is_null($this->preview_version)) {
				$query = $this->sql->prepare('SELECT number, date
				                              FROM version
				                              WHERE NOT number=? AND software_id=?
				                              ORDER BY date DESC
				                              LIMIT 1;');
				$query->bind_param('si', $this->version, $this->id);
				$query->execute();
				$query->bind_result($version, $date);
				$query->fetch();
				$query->free_result();

				$this->preview_version = $version;
				$this->preview_date = $date;
			}
		
		}

		// Set the software version
		// @args: $version -> new software version
		// @rtrn: false if there is one error
		private function setVersion($version) {
			$query = $this->sql->prepare('INSERT INTO version(software_id, number, date) 
			                              VALUES(? , ? , NOW());');
			$query->bind_param('is', $this->id, $version);
			$return = $query->execute();
			$query->free_result();

			if ($return)
				$this->version = $version;

			return $return;
		}

		// Get id
		// @rtrn: the software id
		public function getId() {
			return $this->id;
		}

		// Get name
		// @rtrn: the software name
		public function getName() {
			return $this->name;
		}

		// Get category
		// @rtrn: the software category
		public function getCategory() {
			return $this->category;
		}

		// Get url
		// @rtrn: the url to official website
		public function getUrl() {
			return $this->url;
		}

		// Get url regex
		// @rtrn: the url to check version
		public function getUrlRegex() {
			return $this->url_regex;
		}

		// Get regex
		// @rtrn: regex to find version
		public function getRegex() {
			return $this->regex;
		}

		// Get version
		// @rtrn: the last software version
		public function getVersion() {
			return $this->version;
		}

		// Get preview version
		// @rtrn: the preview software version
		public function getPreviewVersion() {
			$this->previewVersion();
			return $this->preview_version;
		}

		// Get date
		// @rtrn: the date to last version
		public function getDate() {
			return $this->date;
		}

		// Get preview date
		// @rtrn: the date to preview version
		public function getPreviewDate() {
			$this->previewVersion();
			return $this->preview_date;
		}

		// Get list to id
		// @rtrn: array with id
		public function getIdList() {
			$query = 'SELECT id
			          FROM software
			          ORDER BY name;';
			$result = $this->sql->query($query);
			while ($row = $result->fetch_assoc()){
				$data[] = $row['id'];
			}
			$result->free();

			return $data;

		}

	}


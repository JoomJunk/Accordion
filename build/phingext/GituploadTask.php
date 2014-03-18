<?php

require_once "phing/Task.php";
require_once 'phing/tasks/system/MatchingTask.php';
include_once 'phing/util/SourceFileScanner.php';
include_once 'phing/mappers/MergeMapper.php';
include_once 'phing/util/StringHelper.php';

/**
 * Uploads a given set of zips and tars to the appropriate GitHub Repo
 *
 * @author     George Wilson
 * @package    JJ_Accordion
 * @copyright  Copyright (C) 2011 - 2014 JoomJunk. All Rights Reserved
 * @license    GNU General Public License version 3; http://www.gnu.org/licenses/gpl-3.0.html
 */
class GituploadTask extends MatchingTask {

    /**
     * @var string  The owner of the repo
     */
    private $owner;

    /**
     * @var string  The name of the repo
     */
    private $repo;

    protected $fileset = array();
    private $filesets = array();

	/**
     * @var   string  The base directory for the files
     */
    private $baseDir;

	/**
     * Whether to include empty directories.
     */
    private $includeEmpty = true;

	/**
     * The id of the release
     */
    private $version = null;

    /**
     * Add a new fileset.
	 *
     * @return FileSet
     */
    public function createFileSet()
    {
        $this->fileset = new GituploadFileSet();
        $this->filesets[] = $this->fileset;
        return $this->fileset;
    }

    /**
     * Add a new fileset.
	 *
     * @return FileSet
     */
    public function createGituploadFileSet()
    {
        $this->fileset = new GituploadFileSet();
        $this->filesets[] = $this->fileset;
        return $this->fileset;
    }

	/**
     * Set the include empty dirs flag.
     * @param  boolean  Flag if empty dirs should be tarred too
     * @return void
     * @access public
     */
    public function setIncludeEmptyDirs($bool)
    {
        $this->includeEmpty = (boolean) $bool;
    }

    /**
     * This is the base directory to look in for things to zip.
     * @param PhingFile $baseDir
     */
    public function setBasedir(PhingFile $baseDir)
    {
        $this->baseDir = $baseDir;
    }

	/**
	 * This is the id of the release.
	 * @param string $str
	 */
	public function setVersion($str)
	{
		$this->version = $str;
	}

	/**
	 * This is the repo owner.
	 * @param string $str
	 */
	public function setOwner($str)
	{
		$this->owner = $str;
	}

	/**
	 * This is the repo to upload the attachment into.
	 * @param string $str
	 */
	public function setRepo($str)
	{
		$this->repo = $str;
	}

	/**
	 * Gets the extension of a file name
	 *
	 * @param   string  $file  The file name
	 *
	 * @return  string  The file extension
	 *
	 * @since   11.1
	 */
	private function getExt($file)
	{
		$extension = pathinfo($file, PATHINFO_EXTENSION); 

		return $extension; 
	}

	/** 
	 * Send a POST request using cURL
	 *
	 * @param   string   $url      The url and request containing the post information
	 * @param   array    $options  Extra options for cURL. This can also override the defaults
	 *
	 * @return  string The response of the object
	 */
	private function curl_post($url, array $options = array())
	{
		$this->log('Attempting to upload file with URL ' . $url, Project::MSG_INFO);
		$defaults = array(
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 1,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 4,
			CURLOPT_POSTFIELDS => null,
		);

		// Initiate CURL
		$ch = curl_init($url);

		// Create the full params
		$params = array_merge($options, $defaults);
		curl_setopt_array($ch, $params);

		if(!$result = curl_exec($ch)) 
		{
			$this->log(curl_error($ch), Project::MSG_ERR);

			return curl_error($ch);
		}

		curl_close($ch);

		$this->log('Response from server is ' . $result, Project::MSG_INFO);
		return $result;
	}

    /**
     * The main runner to upload the files
	 *
     * @throws BuildException
     */
	public function main()
	{
		// Check some files are defined
		if (empty($this->filesets)) {
			throw new BuildException("You must supply some nested filesets.", $this->getLocation());
		}

		if (!$this->version || !$this->owner || !$this->repo) {
			throw new BuildException("You must supply a version and github details", $this->getLocation());
		}

		foreach($this->filesets as $fs) {

			$files = $fs->getFiles($this->project, $this->includeEmpty);

			$fsBasedir = (null != $this->baseDir) ? $this->baseDir :
								$fs->getDir($this->project);

			$filesToUpload = array();

			// Loop through the filesets
			for ($i=0, $fcount=count($files); $i < $fcount; $i++) {
				$f = new PhingFile($fsBasedir, $files[$i]);
				//$this->log($i . ' for file ' . $f->getPath(), Project::MSG_INFO);			
				$filesToUpload[] = $f;
			}

			// Loop through the files
			foreach ($filesToUpload as $file)
			{
				$filename = basename($file->getPath());
				$this->log('Attempting to upload file with name ' . $filename, Project::MSG_INFO);
				$type = $this->getExt($filename);
				$header = null;

				switch ($type)
				{
					case 'zip':
						$header = 'application/zip';
						break;
					case 'gz':
						$header = 'application/gzip';
						break;
					default:
						$this->log('This does not appear to be a zip or tarball. It is a ' . $type, Project::MSG_INFO);
						break;
				}

				if (!$header)
				{
					 continue;
				}

				$pageUrl = "https://uploads.github.com/repos/" . $this->owner . '/' . $this->repo . "/releases/" . $this->version . "/assets?name=";

				$fullUrl = $pageUrl . $filename;

				$headers = array(
					'Content-Type: ' . $header,
					'Accept: application/vnd.github.manifold-preview',
				);

				$certFile = 'C:\xampp\php\cacert.pem';

				$options = array(
					CURLOPT_HTTPHEADER => $headers,
					CURLOPT_BINARYTRANSFER => 1, // --data-binary
					CURLOPT_POSTFIELDS => file_get_contents(realpath($file->getAbsolutePath())),
					CURLOPT_CAINFO => $certFile,
				);

				$this->curl_post($fullUrl, $options);
			}
		}
    }
}

/**
 * This is a FileSet with the to specify permissions.
 *
 * Permissions are currently not implemented by PEAR Archive_Tar,
 * but hopefully they will be in the future.
 *
 * Taken from Nic's project but with edited class name
 *
 * @author Nicholas K. Dionysopoulos
 * @copyright Copyright (c)2009-2011 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 *
 */
class GituploadFileSet extends FileSet {

    private $files = null;

    /**
     *  Get a list of files and directories specified in the fileset.
     *
     * @param  Project  $p             The fileset to start from
     * @param  boolean  $includeEmpty  Include empty directories?
     *
     *  @return array a list of file and directory names, relative to
     *    the baseDir for the project.
     */
    public function getFiles(Project $p, $includeEmpty = true) {

        if ($this->files === null)
        {
            $ds = $this->getDirectoryScanner($p);
            $this->files = $ds->getIncludedFiles();

            if ($includeEmpty)
            {
                // First any empty directories that will not be implicitly added by any of the files
                $implicitDirs = array();
                foreach ($this->files as $file)
                {
                    $implicitDirs[] = dirname($file);
                }

                $incDirs = $ds->getIncludedDirectories();

                // We'll need to add to that list of implicit dirs any directories
                // that contain other *directories* (and not files), since otherwise
                // we get duplicate directories in the resulting tar
                foreach ($incDirs as $dir)
                {
                    foreach ($incDirs as $dircheck)
                    {
                        if (!empty($dir) && $dir == dirname($dircheck))
                        {
                            $implicitDirs[] = $dir;
                        }
                    }
                }

                $implicitDirs = array_unique($implicitDirs);

                // Now add any empty dirs (dirs not covered by the implicit dirs)
                // to the files array.
                foreach($incDirs as $dir)
                {
                    // We cannot simply use array_diff() since we want to disregard empty/. dirs
                    if ($dir != "" && $dir != "." && !in_array($dir, $implicitDirs))
                    {
                        // it's an empty dir, so we'll add it.
                        $this->files[] = $dir;
                    }
                }
            } // if $includeEmpty
        } // if ($this->files===null)

        return $this->files;
    }
}


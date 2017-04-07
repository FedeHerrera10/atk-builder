<?php
namespace atkbuilder;

use PEAR2\Console\CommandLine\Exception;

/**
 * Class to encapsulate all file system operations.
 * 
 * @author Santiago Ottonello <sanotto@gmail.com>
 */
class FsManager
{
    
    /**
     * Check if a path exists and if not create it
     * 
     * @param string $folder The folder that should exists.
     * @param int $auth the dir mod byte.
     * @return bool true if path exists.
     * @throws Exception if $folders is empty or bad formed
     */
    public static  function ensureFolderExists(string $folder,int $auth=0774) : bool
    {
        if (trim($folder)=="")
        {
            throw new Exception("Empty folder name received by ensureFolderExists");
        }

        $folder = FsManager::normalizePath($folder);
        if(!file_exists($folder))
        {            
            $success=  mkdir($folder,true);            
            if($success === true)
            {
                chmod($folder,$auth);
                FsManager::chown($folder,"www-data:www-data");
                return false;
            }
            else 
            {
                throw new Exception("Couldn't create directory:".$folder);
            }
            
        }
        return true;
    }
	
    /**
     * Create a Folder
     * 
     * @param string $folder
     */
    public static function mkdir(string $folder)
    {
            $folder = FsManager::normalizePath($folder);
            $mkdir = "mkdir -p \"$folder\"";
            system($mkdir);		
    }
    /**
     * Destroy a folder
     * 
     * @param string $folder
     */
    public static function rmdir(string $folder)
    {
            $folder = FsManager::normalizePath($folder);
            $mkdir = "rm -rf \"$folder\"";
            system($mkdir);		
    }
	
    /**
     * Verify that a file does not exist and throw an error if it exists.
     * 
     * @param string $file The file that should not exist.
     * @throws Exception
     */
    public static function assertFileNotExists(string $file)
    {
        $normalized_file = FsManager::normalizePath($file);
        if (file_exists($normalized_file))
        {
            throw new Exception("File or directory allready exists:".$file);
        }
    }

    /**
     * Verify that a file exists, throw an error if not.
     * 
     * @param string $file The File that should exist.
     * @throws Exception
     */
    public static function assertFileExists(string $file)
    {
        $normalized_file = FsManager::normalizePath($file);
        if (!file_exists($normalized_file))
        {
            throw new Exception("File or directory does not exists:".$file);
        }
    }
    /**
     * Create a Sym Link.
     * 
     * @param string $from The origin path.
     * @param string $to The destination path.
     */
    public static function symLink(string $from, string $to)
    {
        $from = FsManager::normalizePath($from);
        $to = FsManager::normalizePath($to);
        symlink($from, $to);
    }
    /**
     * Normalize the path to the system standard.
     * 
     * @param string $path The path to normalize.
     * @return string
     */
    public static function normalizePath(string $path): string
    {
        $path=str_replace("/",  DIRECTORY_SEPARATOR, $path);
        $path=str_replace("//",  DIRECTORY_SEPARATOR, $path);
        $path=str_replace("\\",  DIRECTORY_SEPARATOR, $path);
        $path=str_replace("\\\\",  DIRECTORY_SEPARATOR, $path);
        return $path;
    }
    /**
     * Copy a file.
     * 
     * @param string $from The file to copy
     * @param \atkbuilder\sgtring $to The new file
     */
    public static function copy(string $from, string $to)
    {
        $from = FsManager::normalizePath($from);
        $to = FsManager::normalizePath($to);        
        $copy = " cp -R \"$from\" \"$to\"";        
        system($copy);
    }
 /**
     * Unlink a file.
     * 
     * @param string $file The file to unlimnk (Delete)
     */
    public static function unlink(string $file)
    {
        $file = FsManager::normalizePath($file);
        $unlink = "rm  -f  \"$file\"";
        
        system($unlink);
    }

    /**
     * Copy recursive.
     * 
     * @param string $source The copy origin.
     * @param string $dest The copy destination.
     * @return bool
     */
    public static function copyr(string $source, string $dest): bool
    {
        // Check for symlinks
        if (is_link($source)) 
        {
            return symlink(readlink($source), $dest);
        }	
        // Simple copy for a file
        if (is_file($source)) 
        {
            return copy($source, $dest);
        }
        // Make destination directory
        if (!is_dir($dest)) 
        {
            mkdir($dest);
        }
        // Loop through the folder
       
        $dir = dir($source);
        while (false !== ($entry = $dir->read())) 
        {
            // Skip pointers
            if ($entry == '.' || $entry == '..') 
            {
                continue;
            }
            // Deep copy directories
            FsManager::copyr("$source/$entry", "$dest/$entry");
        }
        // Clean up
        $dir->close();
        return true;
    }
    /**
     * Change file mode.
     * 
     * @param string $from The file to change mode.
     * @param string $auth The mode string.
     */
    public static function chmod(string $from, string $auth)
    {
            /*
            $from = FsManager::normalizePath($from);

            $GLOBALS['syslog']->debug("Chmod -R".$auth." ".$from." from:".$from,1);
            $chmod = " chmod -R ${auth} \"$from\" ";
            $GLOBALS['syslog']->debug($chmod,2);
            system($chmod);
    $GLOBALS['syslog']->debug("Chown -R www-data:www-data $from from:".$from,1);
            $chmod = " chown -R www-data:www-data \"$from\" ";
            $GLOBALS['syslog']->debug($chmod,2);
            system($chmod);
            */
    }
    /**
     * Change file owner.
     *
     * @param string $from The file.
     * @param string $own The new owner.
     */
    public static function chown(string $from, string $own)
    {
            /*
            $from = FsManager::normalizePath($from);

            $GLOBALS['syslog']->debug("Chown -R ".$own." ".$from." from:".$from,1);
            $chown = " chown -R ${own} \"$from\" ";
            $GLOBALS['syslog']->debug($chown,2);
            system($chown);
            */
    }
    /**
     * Create a file with the given contents.
     * 
     * @param string $file The file to write.
     * @param string $contents The contents for the file.
     * @throws Exception
     */
    public static function filePutContents(string $file, string $contents)
    {
        $file_normalized = FsManager::normalizePath($file);
        $bytes_written=file_put_contents($file_normalized, $contents);
        
        if ($bytes_written === false)
        {
                throw new Exception("Could'nt write file:".$file_normalized."(If --base-name specified, check base name)");
        }
        
        chmod($file_normalized,0774);
    }
    /**
     * Retrieves the contents of a file.
     * 
     * @param string $file The file to get the contents
     * @return string The contents.
     */
    public static function fileGetContents(string $file):string
    {
        $contents = file_get_contents($file);       
        return $contents;
    }
 
    /**
     * Check if a file exists.
     * 
     * @param string $file The file path.
     * @return bool true if file exists.
     * 
     */ 
    public static function fileExists(string $file):bool
    {
        $file = FsManager::normalizePath($file);
        return file_exists($file);
    }
}
?>

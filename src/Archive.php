<?php



namespace Solenoid\Zip;



use \Solenoid\System\Directory;
use \Solenoid\System\Resource;



class Archive
{
    private string      $file_path;
    private \ZipArchive $archive;



    # Returns [self]
    public function __construct (string $file_path)
    {
        // (Getting the values)
        $this->file_path = $file_path;
        $this->archive   = new \ZipArchive();
    }

    # Returns [Archive]
    public static function select (string $file_path)
    {
        // Returning the value
        return new Archive( $file_path );
    }



    # Returns [bool]
    public function extract (string $folder_path, ?string $password = null)
    {
        // (Creating a ZipArchive)
        $zip_archive = new \ZipArchive();



        if ( $zip_archive->open( $this->file_path ) === false )
        {// (Unable to open the archive)
            // (Setting the value)
            $message = "Unable to open the zip archive";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }

        

        if ( $password )
        {// Value found
            if ( !$zip_archive->setPassword( $password ) )
            {// (Unable to set the password)
                // (Setting the value)
                $message = "Unable to set the password";

                // Throwing an exception
                throw new \Exception($message);

                // Returning the value
                return false;
            }
        }



        if ( !$zip_archive->extractTo( $folder_path ) )
        {// (Unable to extract the zip archive)
            // (Setting the value)
            $message = "Unable to extract the zip archive";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        if ( !$zip_archive->close() )
        {// (Unable to close the zip archive)
            // (Setting the value)
            $message = "Unable to close the zip archive";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    # Returns [bool] | Throws [Exception]
    public function build (string $src_folder_path, ?string $password = null)
    {
        if ( $this->archive->open( $this->file_path, \ZipArchive::CREATE ) === false )
        {// (Unable to create the zip archive)
            // (Setting the value)
            $message = "Unable to create the zip archive";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        if ( $password )
        {// Value found
            if ( !$this->archive->setPassword( $password ) )
            {// (Unable to set the password)
                // (Setting the value)
                $message = "Unable to set the password";

                // Throwing an exception
                throw new \Exception($message);

                // Returning the value
                return false;
            }
        }



        // (Getting the value)
        $directory = Directory::select( $src_folder_path )->resolve();

        if ( $directory === false )
        {// (Directory not found)
            // (Setting the value)
            $message = "Cannot build the zip archive :: Input path not found";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // (Getting the value)
        $resources = $directory->list();

        foreach ($resources as $src)
        {// Processing each entry
            // (Getting the values)
            $resource = Resource::select( $src );
            $dst      = $resource->diff( $directory );



            if ( $resource->is_file() )
            {// (Resource is a file)
                if ( !$this->archive->addFile( $src, $dst ) )
                {// (Unable to add the resource to the zip archive)
                    // (Setting the value)
                    $message = "Unable to add the resource to the zip archive";

                    // Throwing an exception
                    throw new \Exception($message);

                    // Returning the value
                    return false;
                }
            }
            else
            if ( $resource->is_dir() )
            {// (Resource is a directory)
                if ( !$this->archive->addEmptyDir( $dst ) )
                {// (Unable to add an empty directory to the zip archive)
                    // (Setting the value)
                    $message = "Unable to add an empty directory to the zip archive";

                    // Throwing an exception
                    throw new \Exception($message);

                    // Returning the value
                    return false;
                }
            }
        }



        if ( !$this->archive->close() )
        {// (Unable to close the zip archive)
            // (Setting the value)
            $message = "Unable to close the zip archive";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->file_path;
    }
}



?>
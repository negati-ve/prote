<?php
namespace Upload;
class UploadInterface {
	private $Service=NULL;
	public function __construct(\DIC\Service $Service){
		$this->Service=$Service;
	}

	public function upload_csv(){
		$storage = new \Upload\Storage\FileSystem($this->Service->Config()->get_basepath().'/static/uploads');
		$file = new \Upload\File('file', $storage);

		// Optionally you can rename the file on upload
		$new_filename = uniqid();
		$file->setName($new_filename);

		// Validate file upload
		// MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
		$file->addValidations(array(
		    // Ensure file is of type "image/png"
		    new \Upload\Validation\Mimetype(array('text/csv','text/plain')),

		    //You can also add multi mimetype validation
		    //new \Upload\Validation\Mimetype(array('image/png', 'image/gif'))

		    // Ensure file is no larger than 5M (use "B", "K", M", or "G")
		    new \Upload\Validation\Size('5M')
		));

		// Access data about the file that has been uploaded
		$data = array(
		    'name'       => $file->getNameWithExtension(),
		    'extension'  => $file->getExtension(),
		    'mime'       => $file->getMimetype(),
		    'size'       => $file->getSize(),
		    'md5'        => $file->getMd5(),
		    'dimensions' => $file->getDimensions()
		);
		// var_dump($data);

		// Try to upload file
		try {
		    // Success!
		    if($file->upload()){
		    	return $data;
		    }
		} catch (\Exception $e) {
		    // Fail!
		    $errors = $file->getErrors();
		    var_dump($errors);
		}
	}
}
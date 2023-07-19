<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\{AdminLoginRequest, QRCodeRequest, ExportRequest};
use Illuminate\Support\Facades\{Auth, File};
use App\Models\{BackendModel, User};
use App\Imports\{QRCodeImport};
use Illuminate\Support\{Str, Arr};
use Hash;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManagerStatic;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;


class BackendController extends Controller
{
    public function checkAdminLogin(AdminLoginRequest $request){
        $userdata = [
            'email'     => $request->validated('email'),
            'password'  => $request->validated('password')
        ];
        $remember_me = ($request->has('remember')) ? true : false;

        if (Auth::attempt($userdata,$remember_me)) {
            return redirect('admin/home');
        } else {
            return back()->with([
                'status' => 'failed',
                "message" => "Authentication failed",
            ]);
        }
    }
      
    public function registerAdmin(Request $request){
        $data = $request->all();

        $rules = array(
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        );
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            // dd($validator);
            return back()->withErrors($validator)->withInput($request->except('password'));
        } else {
            $insert = User::create([
                'uuid' => Str::uuid()->toString(),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
                // 'password' => $data['password']
            ]);
            /*$user = new User();
            $user = User::where('uuid', $uuid)->first();*/
            $insert->roles()->attach(2);
            if(!$insert){
                exit(json_encode(array('status'=>'failed', 'message'=>'Something went wrong, please try again after sometime')));
            }
            echo json_encode(array('status'=>'success', 'message'=>'Registration successful'));
        }
    }

    public function qrCodeGenerate(QRCodeRequest $request){
        try {
            $qrCodeImport = new QRCodeImport;
            $importDataArr = Excel::import($qrCodeImport,$request->validated('file'));
            $totalDataCount = $qrCodeImport->getRowCount();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $dataError['rows'] = $failure->row();
                $dataError['attribute'] = $failure->attribute();
                $dataError['errors'] = $failure->errors();
                $dataError['values'] = $failure->values();
            }
            return back()->with('upload-failed',$dataError);
        }
        return back()->with('upload-success','File uploaded successfully');
    }

    public function downloadZip($folderName){
        $pathForZip = "outletQRCodeZip";
        // dd(public_path($pathForZip.$folderName.'.zip'));
        if(File::exists(public_path($pathForZip . DIRECTORY_SEPARATOR . $folderName.'.zip'))){
            return response()->download(public_path($pathForZip . DIRECTORY_SEPARATOR . $folderName.'.zip'));
        }else{
            $uniqueFileName = \Carbon\Carbon::now()->format('d-M-h-i-sa');
            $publicPath = public_path('images/qr-code-final');
            if(!File::exists(public_path($pathForZip))){
                File::makeDirectory(public_path($pathForZip),0777,true);
            }
            $filePath = $pathForZip . DIRECTORY_SEPARATOR . $folderName.'.zip';
            $zipGenerated = self::generateZip($publicPath . DIRECTORY_SEPARATOR . $folderName, $filePath);
            if($zipGenerated){
                return response()->download(public_path($filePath));
            }
        }

    }

    public function downloadZipForPrintableFiles($folderName){
        $pathForPrintableFilesZip = "printableFilesZip";
        // dd(public_path($pathForZip.$folderName.'.zip'));
        if(File::exists(public_path($pathForPrintableFilesZip . DIRECTORY_SEPARATOR . $folderName.'.zip'))){
            return response()->download(public_path($pathForPrintableFilesZip . DIRECTORY_SEPARATOR . $folderName.'.zip'));
        }else{
            $uniqueFileName = \Carbon\Carbon::now()->format('d-M-h-i-sa');
            $publicPath = public_path('images/printables');
            if(!File::exists(public_path($pathForPrintableFilesZip))){
                File::makeDirectory(public_path($pathForPrintableFilesZip),0777,true);
            }
            $filePath = $pathForPrintableFilesZip . DIRECTORY_SEPARATOR . $folderName.'.zip';
            $zipGenerated = self::generateZip($publicPath . DIRECTORY_SEPARATOR . $folderName, $filePath);
            if($zipGenerated){
                return response()->download(public_path($filePath));
            }
        }

    }

    public function generateZip($publicPath, $filePath){
        $filePath = public_path($filePath);
        $zip = new \ZipArchive();


        if ($zip->open($filePath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot open ' . $filePath);
        }

        self::addContent($zip, $publicPath);
        $zip->close();
        return true;
    }
    private function createFolder($folder,$wdCode){
        $srNo = 1;
        $publicPath = '';
        if(File::exists(public_path($folder))){
            while(File::exists(public_path('images/qr-code-final/'.Carbon::now()->format('d-m-Y').'-'.$srNo.'/'.$wdCode))){
                $srNo++;
            }
        }else{
            $srNo = "";
        }
        if($srNo==""){
            $publicPath = $folder;
        }else{
            $publicPath = 'images/qr-code-final/'.Carbon::now()->format('d-m-Y').'-'.$srNo;
            // $createFolder = File::makeDirectory(public_path('images/qr-code-final/'.Carbon::now()->format('d-m-Y').'-'.$srNo.'/'.$wdCode),0777,true);
        }
        $createFolder = File::makeDirectory(public_path($publicPath),0777,true);
        // dd($createFolder);
        return $publicPath;
    }

    private function addContent(\ZipArchive $zip, string $path){
        /** @var SplFileInfo[] $files */
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($path) + 1);

                if (!$iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
    }


    public static function directoryLists($qrCodePublicPath){
        // return DIRECTORY_SEPARATOR;
        $result = [];

        $currentDirectory = scandir($qrCodePublicPath);
        // return $currentDirectory;
        $id = 1;
        foreach ($currentDirectory as $key => $value)
        {
            if(!in_array($value, array(".","..")))
            {
                $fullPath = $qrCodePublicPath . DIRECTORY_SEPARATOR . $value;
                // $id = $key;
                // dd($fullPath, Carbon::parse(filemtime($fullPath))->format('d-m-Y h:i A'));

                $fullPath = $qrCodePublicPath . DIRECTORY_SEPARATOR . $value;
                if (is_dir($fullPath))
                {
                     $result[$value] = self::directoryLists($fullPath);
                }
                else
                {
                    $result[] = $fullPath;
                }
            }
       }

       return $result;
    }

    public function generateImage($folderName){
        $publicPath = public_path('images/qr-code-final') . DIRECTORY_SEPARATOR . $folderName;
        $printableFolderPath = public_path('images/printables' . DIRECTORY_SEPARATOR . $folderName);
        if(!File::exists($printableFolderPath)){
            File::makeDirectory($printableFolderPath,0777,true);
        }
        // dd($printableFolderPath);

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $images = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $publicPath,
                \FilesystemIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                // dd($iterator);
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($publicPath) + 1);

                if (!$iterator->isDir()) {
                        $images[] = $relativePath;
                }
            }
            $iterator->next();
        }
        // dd($count);
        $noofQRCodeInOnePrintableFile = 504; // 36 rows
        $artboardWidth = 56;
        $qrcodeWidth = $qrcodeHeight = 4;
        $noofRows = (int) ceil(((count($images)*$qrcodeWidth)/$artboardWidth));
        // $noofRows = (int) ceil(((15*$qrcodeWidth)/$artboardWidth));

        $collection = collect($images)->chunk($noofQRCodeInOnePrintableFile)->toArray();
        $noofPrintableImgFiles = (int) ceil(count($images)/$noofQRCodeInOnePrintableFile);
        // dd(count($collection[1]),array_key_last($collection[1]),$noofRows,$noofPrintableImgFiles); // 784

        /*$width2 = self::inchToPixel(58); // for padding
        $height2 = self::inchToPixel($noofRows*$qrcodeHeight); // for padding
        $bg = ImageManagerStatic::canvas($width2, $height2, '#FFFFFF');*/
        $width = self::inchToPixel(56);
        $height = self::inchToPixel(144);
        $secondLoop = 0;
        // dd($width,$height);
        for($i=0; $i < $noofPrintableImgFiles; $i++){
            $image = ImageManagerStatic::canvas($width, $height, '#FFFFFF');
            // $image->save("artboard.jpg");
            if($i==0){
                $secondLoop = 0;
                $printableFilePath = $printableFolderPath . '/' . $folderName . '.jpg';
            }else{
                $secondLoop += $noofQRCodeInOnePrintableFile;
                $printableFilePath = $printableFolderPath . '/' . $folderName . '-' . $i . '.jpg';
            }
            for ($j = $secondLoop; $j < array_key_last($collection[$i])+1; $j++) {
                // dump($j, $images[$j]);
                $x = (($j-$secondLoop) % 14) * 4 * 96;
                $y = floor(($j-$secondLoop) / 14) * 4 * 96;

                $src = ImageManagerStatic::make($publicPath . DIRECTORY_SEPARATOR . $images[$j]);
                // $src = ImageManagerStatic::make("http://dsordercapture.in/public/images/qr-code-final/23-01-2023/PU3901/420807.png");
                $image->insert($src, 'top-left', $x, $y);
            }
            // $bg->insert($image, 'top-left', 96, 96);

            // Save the generated image to a file on your local system

            $image->save($printableFilePath);
        }
        // dd('end');
        return back()->with('qrcode-generation-success', 'The zip file is generated');
        // return response()->download($printableFilePath);
    }
    public static function inchToPixel($value)
    {
        return $value * 96;
    }
    public function savePrintableQRCode($value=''){
        if(!File::exists(public_path($pathForZip))){
            File::makeDirectory(public_path($pathForZip),0777,true);
        }
    }

    /* Add */

    /* Edit */

    /* Delete */


    /* Export */
    public function exportData(ExportRequest $request){
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        $table = $request->validated('table');

        $startDate = Carbon::parse($request->validated('start_date'))->format('Y-m-d 00:00:00');
        $endDate = Carbon::parse($request->validated('end_date'))->format('Y-m-d 23:59:59');

        $fileName = config('constants.export_filename_prefix').$table.'-'.now()->format('Y-m-d-h:i:sa').".xlsx";
        $exportTableName = Str::singular(Str::ucfirst(Str::camel($table)));
        $exportClassName = "App\Exports\\".$exportTableName."Export";

        // dd($exportClassName);

        if(class_exists($exportClassName)===true){
            $classInstance = new $exportClassName;
            $classInstance->dateRange($startDate,$endDate);
            return Excel::download($classInstance, $fileName);
        }else{
            $table = ucwords(str_replace('_', ' ', Str::singular($table)));
            return back()->withErrors(['classNotFoundError' => "The export for {$table} table does not exist please try again later or contact administrator."])->withInput($request->validated());
        }
    }

}

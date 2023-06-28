<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\Filesystem;
use Google\Cloud\Storage\StorageClient;

class GoogleStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Storage::extend('gcs', function($app, $config){
            $prefix = '';
            if(config('app.env') == 'production'){
                $prefix = 'itc-xplore';
            }else{
                $prefix = 'itc-xplore-staging';
            }

            $clientOptions = [
                'projectId' => $config['project_id'],
                'keyFilePath' => $config['key_file'],
            ];
            $storageClient = new StorageClient($clientOptions);
            $bucket = $storageClient->bucket($config['bucket']);

            $adapter = new GoogleCloudStorageAdapter($bucket, $prefix);

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );

        });
    }
}

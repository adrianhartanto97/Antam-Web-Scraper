<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Exception;
use Storage;
use File;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector;

class GetPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_price:antam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get price of Antam Gold per 1gram';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $web = $client->get('https://harga-emas.org/');
        $html = $web->getBody()->getContents();

        $crawler = new Crawler($html);

        //crawling for the first table, then the fourth row, then ninth column to get the HTML node. 
        $res = $crawler->filter('.in_table')->eq(0)->filter('tr')->eq(3)->children()->eq(8)->html();
        
        //$res will output "665.000 (-2000)", so I split it into two elements by space delimiter.
        $arr = explode(" ", $res);

        date_default_timezone_set('Asia/Jakarta');

        //it will output "2018-11-20 : IDR 665.000"
        $string = date("Y-m-d"). " : IDR ". $arr[0];

        //Save to local text file "document.text"
        Storage::disk('local')->append('document.txt',$string);
    }
}

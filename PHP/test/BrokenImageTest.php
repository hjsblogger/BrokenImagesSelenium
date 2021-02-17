<?php
require 'vendor/autoload.php';
 
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
 
$GLOBALS['LT_USERNAME'] = "user-name";
# accessKey:  AccessKey can be generated from automation dashboard or profile section
$GLOBALS['LT_APPKEY'] = "access-key";
 
class BrokenImageTest extends TestCase
{
 
  protected $webDriver;
  
  public function build_browser_capabilities(){
    /* $capabilities = DesiredCapabilities::chrome(); */
    $capabilities = array(
      "build" => "[PHP] Finding broken images on a webpage using Selenium",
      "name" => "[PHP] Finding broken images on a webpage using Selenium",
      "platform" => "macOS High Sierra",
      "browserName" => "MicrosoftEdge",
      "version" => "latest"
    );
    return $capabilities;
  }
  
  public function setUp(): void
  {
    $capabilities = $this->build_browser_capabilities();
    /* Download the Selenium Server 3.141.59 from 
    https://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.59.jar
    */
    $url = "https://". $GLOBALS['LT_USERNAME'] .":" . $GLOBALS['LT_APPKEY'] ."@hub.lambdatest.com/wd/hub";
    $this->webDriver = RemoteWebDriver::create($url, $capabilities);
  }
 
  public function tearDown(): void
  {
    $this->webDriver->quit();
  }

  /*
  * @test
  */ 
  public function test_searchbrokenimages()
  {
    $test_url = "https://the-internet.herokuapp.com/broken_images";
    # For site with absolute path
    # $test_url = "https://www.lambdatest.com/blog";
    # End - For site with absolute path
    $base_url = "https://the-internet.herokuapp.com/";
    
    $driver = $this->webDriver;
    $driver->get($test_url);
    $this->assertEquals("The Internet", $driver->getTitle());
    # For site with absolute path
    # $this->assertEquals("LambdaTest | A Cross Browser Testing Blog", $driver->getTitle());
    # End - For site with absolute path
    $driver->manage()->window()->maximize();
 
    $iBrokenImageCount = 0;
    
    /* file_get_contents is used to get the page's HTML source */
    $html = file_get_contents($test_url);
 
    /* Instantiate the DOMDocument class */
    $htmlDom = new DOMDocument;
 
    /* The HTML of the page is parsed using DOMDocument::loadHTML */
    @$htmlDom->loadHTML($html);


    /* Extract the links from the page */
    $image_list = $htmlDom->getElementsByTagName('img');

    /* The DOMNodeList object is traversed to check for its validity */
    foreach($image_list as $img)
    {
        $img_path = $img->getAttribute('src');
        # Convert relative path to absolute path
        $search_path = "/" . $img_path;
        $abs_path = relative2absolute($search_path, $base_url);
        # When absolute path is used for fetching the images
        # For site with absolute path
        # $abs_path = $img_path;
        # For site with absolute path

        $response = @get_headers($abs_path);
        if (preg_match("|200|", $response[0]))
        {
          print($abs_path . " is not broken\n");
        }
        else
        {
          print($abs_path . " is broken\n");
          $iBrokenImageCount = $iBrokenImageCount + 1;
        }
    }
    print("\nThe page " . $test_url . " has " . $iBrokenImageCount . " broken images");
  }
}
?>

<?php
    function relative2absolute($rel_path, $base_path)
    {
        /* return if already absolute URL */
        if (parse_url($rel_path, PHP_URL_SCHEME) != '') return $rel_path;
    
        /* queries and anchors */
        if ($rel_path[0]=='#' || $rel_path[0]=='?') return $base_path.$rel_path;
    
        /* parse base URL and convert to local variables:
            $scheme, $host, $path */
        extract(parse_url($base_path));
    
        /* remove non-directory element from path */
        $new_path = preg_replace('#/[^/]*$#', '', $base_path);
    
        /* destroy path if relative url points to root */
        if ($rel_path[0] == '/') $new_path = '';
    
        /* dirty absolute URL */
        $abs_path = "$host$new_path/$rel_path";
    
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $repl = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($counter=1; $counter>0; $abs_path=preg_replace($repl, '/', $abs_path, -1, $counter)) {}
    
        /* absolute URL is ready! */
        return $scheme.'://'.$abs_path;
    }
?>
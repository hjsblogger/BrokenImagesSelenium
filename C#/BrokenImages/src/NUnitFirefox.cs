using OpenQA.Selenium;
using OpenQA.Selenium.Remote;
using OpenQA.Selenium.Chrome;
using NUnit.Framework;
using System.Threading;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using OpenQA.Selenium.Remote;
using System;
using System.Threading;
using System.Net.Http;
using System.Threading.Tasks;

namespace TestBrokenImages
{
    [TestFixture("chrome", "latest", "Windows 10")]
    public class TestBrokenImages
    {
        private String browser;
        private String version;
        private String os;

        IWebDriver driver;

        public TestBrokenImages(String browser, String version, String os)
        {
            this.browser = browser;
            this.version = version;
            this.os = os;
        }

        [SetUp]
        public void Init()
        {
            String username = "user-name";
            String accesskey = "access-key";
            String gridURL = "@hub.lambdatest.com/wd/hub";

            DesiredCapabilities capabilities = new DesiredCapabilities();

            capabilities.SetCapability("user", username);
            capabilities.SetCapability("accessKey", accesskey);
            capabilities.SetCapability("browserName", browser);
            capabilities.SetCapability("version", version);
            capabilities.SetCapability("platform", os);
            capabilities.SetCapability("build", "[C#] Finding broken images on a webpage using Selenium");
            capabilities.SetCapability("name", "[C#] Finding broken images on a webpage using Selenium");

            driver = new RemoteWebDriver(new Uri("https://" + username + ":" + accesskey + gridURL), capabilities, TimeSpan.FromSeconds(600));

            System.Threading.Thread.Sleep(2000);
        }

        [Test]
        public async Task LT_Broken_Images_Test()
        {
            int broken_images = 0;
            String test_url = "https://the-internet.herokuapp.com/broken_images";
            driver.Url = test_url;
            using var client = new HttpClient();
            var image_list = driver.FindElements(By.TagName("img"));

            /* Loop through all the images */
            foreach (var img in image_list)
            {
                try
                {
                    /* Get the URI */
                    HttpResponseMessage response = await client.GetAsync(img.GetAttribute("src"));
                    /* Reference - https://docs.microsoft.com/en-us/dotnet/api/system.net.httpwebresponse.statuscode?view=netcore-3.1 */
                    if (response.StatusCode == HttpStatusCode.OK)
                    {
                        System.Console.WriteLine("Image at the link " + img.GetAttribute("src") + " is OK, status is "
                                + response.StatusCode);
                    }
                    else
                    {
                        System.Console.WriteLine("Image at the link " + img.GetAttribute("src") + " is Broken, status is "
                                + response.StatusCode);
                        broken_images++;
                    }
                }
                catch (Exception ex)
                {
                    if ((ex is ArgumentNullException) ||
                       (ex is NotSupportedException))
                    {
                        System.Console.WriteLine("Exception occured\n");
                    }
                }
            }
            /* Perform wait to check the output */
            System.Threading.Thread.Sleep(2000);
            Console.WriteLine("\nThe page " + test_url + " has " + broken_images + " broken images");
        }

        [TearDown]
        public void Cleanup()
        {
            if (driver != null)
                driver.Quit();
        }
    }
}
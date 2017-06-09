<?php

namespace Tests\AppBundle\Mailjet\Transport;

use AppBundle\Mailjet\MailjetClient;
use AppBundle\Mailjet\Transport\MailjetApiTransport;
use AppBundle\Mailjet\MailjetTemplateEmail;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Psr7\Response as HttpResponse;
use Ramsey\Uuid\Uuid;

class MailjetApiTransportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \AppBundle\Mailjet\Exception\MailjetException
     * @expectedExceptionMessage Unable to send email to recipients.
     */
    public function testCannotSendTemplateEmail()
    {
        $httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $httpClient->expects($this->once())->method('request')->willReturn(new HttpResponse(400));

        $client = new MailjetClient($httpClient, 'public-key', 'private-key');
        $transport = new MailjetApiTransport($client);
        $transport->sendTemplateEmail($this->createDummyEmail());
    }

    public function testSendTemplateEmail()
    {
        $email = $this->createDummyEmail();

        $body = <<<'EOF'
{
    "Sent": [
        {
            "Email": "john.smith@example.tld",
            "MessageID": 111111111111111
        }
    ]
}
EOF;

        $httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with('POST', 'send', [
                'auth' => ['public-key', 'private-key'],
                'body' => json_encode($email->getBody()),
            ])
            ->willReturn(new HttpResponse(200, [], $body))
        ;

        $client = new MailjetClient($httpClient, 'public-key', 'private-key');
        $transport = new MailjetApiTransport($client);
        $transport->sendTemplateEmail($email);

        $this->assertSame($body, $email->getHttpResponsePayload());
    }

    private function createDummyEmail()
    {
        $email = new MailjetTemplateEmail(Uuid::uuid4(), '12345', 'Votre donation !', 'contact@en-marche.fr', 'En Marche !');
        $email->addRecipient('john.smith@example.tld', 'John Smith', ['name' => 'John Smith']);

        return $email;
    }
}

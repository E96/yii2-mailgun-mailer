<?php

namespace e96\mailgunmailer;

use yii\base\NotSupportedException;
use yii\mail\BaseMessage;
use Mailgun\Messages\MessageBuilder;

/**
 * Message implements a message class based on Mailgun.
 *
 * @property string $from
 * @property array $message
 * @property array $tags
 * @property boolean|string $clickTracking
 * @property boolean $opensTracking
 * @property integer|string $campaignId
 * @property boolean $dkim
 * @property boolean $testMode
 */
class Message extends BaseMessage
{
    /**
     * @var MessageBuilder Mailgun message instance.
     */
    private $_messageBuilder;

    private $_tags;
    private $_clickTracking;
    private $_opensTracking;
    private $_campaignId;
    private $_dkim;
    private $_testMode;

    /**
     * @return MessageBuilder email message instance.
     */
    public function getMessageBuilder()
    {
        if (!is_object($this->_messageBuilder)) {
            $this->_messageBuilder = $this->createMessageBuilder();
        }

        return $this->_messageBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return isset($this->message['from']) ? $this->message['from'] : null;
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        /**
         * Added compliance with swift_mailer to set Name <email> like ['email' => 'name']
         */
        $variables = [];
        if(is_array($from)) {
            $email = each($from);
            $from = $email['key'];
            $variables['full_name'] = $email['value'];
        }
        $this->getMessageBuilder()->setFromAddress($from, $variables);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return isset($this->message['h:reply-to']) ? $this->message['h:reply-to'] : null;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->getMessageBuilder()->setReplyToAddress($replyTo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return isset($this->message['to']) ? $this->message['to'] : null;
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->getMessageBuilder()->addToRecipient($to);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return isset($this->message['cc']) ? $this->message['cc'] : null;
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->getMessageBuilder()->addCcRecipient($cc);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return isset($this->message['bcc']) ? $this->message['bcc'] : null;
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->getMessageBuilder()->addBccRecipient($bcc);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return isset($this->message['subject']) ? $this->message['subject'] : null;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->getMessageBuilder()->setSubject($subject);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->getMessageBuilder()->setTextBody($text);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->getMessageBuilder()->setHtmlBody($html);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $this->getMessageBuilder()->addAttachment(
            $fileName,
            (isset($options['fileName']) ? $options['fileName'] : null)
        );
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        throw new NotSupportedException('attach content is not supported');
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $this->getMessageBuilder()->addInlineImage(
            $fileName,
            (isset($options['fileName']) ? $options['fileName'] : null)
        );
        return null;
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        throw new NotSupportedException('embed content is not supported');
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return print_r($this->message, true);
    }

    /**
     * @param array $tags
     * @return $this
     * @throws \Mailgun\Messages\Exceptions\TooManyParameters
     */
    public function setTags($tags)
    {
        foreach ($tags as $tag) {
            $this->_tags = $this->getMessageBuilder()->addTag($tag);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * Set click tracking
     * @param boolean|string $mode true, false, "html"
     * @return $this
     */
    public function setClickTracking($mode)
    {
        $this->_clickTracking = $this->getMessageBuilder()->setClickTracking($mode);
        return $this;
    }

    /**
     * @return boolean|string
     */
    public function getClickTracking()
    {
        return $this->_clickTracking;
    }

    /**
     * @param boolean $enabled
     * @return $this
     */
    public function setOpensTracking($enabled)
    {
        $this->_opensTracking = $this->getMessageBuilder()->setOpenTracking($enabled);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getOpensTracking()
    {
        return $this->_opensTracking;
    }

    /**
     * @param string|int $id
     * @return $this
     * @throws \Mailgun\Messages\Exceptions\TooManyParameters
     */
    public function setCampaignId($id)
    {
        $this->_campaignId = $this->getMessageBuilder()->addCampaignId($id);
        return $this;
    }

    /**
     * @return string|int
     */
    public function getCampaignId()
    {
        return $this->_campaignId;
    }

    /**
     * @param boolean $enabled
     * @return $this
     */
    public function setDkim($enabled)
    {
        $this->_dkim = $this->getMessageBuilder()->setDkim($enabled);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getDkim()
    {
        return $this->_dkim;
    }

    /**
     * @param boolean $enabled
     * @return $this
     */
    public function setTestMode($enabled)
    {
        $this->_testMode = $this->getMessageBuilder()->setTestMode($enabled);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getTestMode()
    {
        return $this->_testMode;
    }

    /**
     * @return Array message object
     */
    public function getMessage()
    {
        return $this->getMessageBuilder()->getMessage();
    }

    /**
     * @return Array files list
     */
    public function getFiles()
    {
        return $this->getMessageBuilder()->getFiles();
    }

    /**
     * Creates the Mailgun email message instance.
     * @return MessageBuilder email message instance.
     */
    protected function createMessageBuilder()
    {
        return new MessageBuilder();
    }
}

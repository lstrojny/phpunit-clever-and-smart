<?php
namespace PHPUnit\Runner\CleverAndSmart;

use DateTime;

class Run
{
    /** @var string */
    private $runId;

    /** @var DateTime */
    private $ranAt;

    public function __construct($runId = null, DateTime $ranAt = null)
    {
        $this->runId = $runId ?: Util::createRunId();
        $this->ranAt = $ranAt ?: new DateTime();
    }

    /**
     * @return string
     */
    public function getRunId()
    {
        return $this->runId;
    }

    /**
     * @return DateTime
     */
    public function getRanAt()
    {
        return $this->ranAt;
    }
}

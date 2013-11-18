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
        $this->runId = $runId ?: Util::getRunId();
        $this->ranAt = $ranAt ?: DateTime::createFromFormat('U.u', microtime(true));
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

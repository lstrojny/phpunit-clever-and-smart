<?php
namespace PHPUnit\Runner\CleverAndSmart;

use DateTime;

class Run
{
    /** @var string */
    private $runId;

    /** @var float */
    private $ranAt;

    public function __construct($runId = null, $ranAt = null)
    {
        $this->runId = $runId ?: Util::createRunId();
        $this->ranAt = $ranAt ?: microtime(true);
    }

    /**
     * @return string
     */
    public function getRunIdentifier()
    {
        return $this->runId;
    }

    /**
     * @return float
     */
    public function getRanAt()
    {
        return $this->ranAt;
    }
}

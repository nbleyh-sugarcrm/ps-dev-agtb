<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

require_once('modules/SchedulersJobs/SchedulersJob.php');

class SugarJobKBSContentUpdateArticles implements RunnableSchedulerJob
{

    /**
     * @var $job Job object.
     */
    protected $job;

    /**
     * @var TimeDate $td
     */
    protected $td;

    /**
     * Sets the SchedulersJob instance for the class.
     *
     * @param SchedulersJob $job
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * Handles processing SchedulersJobs.
     *
     * @param Mixed $data Passed in from the job_queue.
     * @return bool True on success, false on error.
     */
    public function run($data)
    {
        $this->td = new TimeDate();

        // Expired articles.
        foreach ($this->getExpiredArticles() as $article) {
            $bean = BeanFactory::getBean('KBSContents', $article['id']);
            $bean->exp_date = $this->td->nowDate();
            $bean->status = KBSContent::ST_EXPIRED;
            $bean->save();
        }

        // Approved articles.
        foreach ($this->getApprovedArticles() as $article) {
            $bean = BeanFactory::getBean('KBSContents', $article['id']);

            if ($bean->exp_date && strtotime($bean->exp_date) <= strtotime($this->td->nowDate())) {
                $bean->exp_date = $this->td->nowDate();
                $bean->status = KBSContent::ST_EXPIRED;
            } else {
                if ($bean->internal_rev) {
                    $bean->status = KBSContent::ST_PUBLISHED_IN;
                } else {
                    $bean->status = KBSContent::ST_PUBLISHED;
                }
            }
            $bean->active_date = $this->td->nowDate();
            $bean->save();
        }
        return $this->job->succeedJob();
    }

    /**
     * Returns expired articles.
     *
     * @return array Of IDs.
     */
    protected function getExpiredArticles()
    {
        $sq = new SugarQuery();
        $sq->select(array('id'));
        $sq->from(BeanFactory::getBean('KBSContents'));
        $sq->where()
            ->in('status', array('published-in', 'published-ex', 'published'))
            ->lte('exp_date', $this->td->nowDbDate());
        return $sq->execute();
    }

    /**
     * Returns approved articles.
     *
     * @return array Of IDs.
     */
    protected function getApprovedArticles()
    {
        $sq = new SugarQuery();
        $sq->select(array('id'));
        $sq->from(BeanFactory::getBean('KBSContents'));
        $sq->where()
            ->equals('status', 'approved')
            ->lte('active_date', $this->td->nowDbDate());
        return $sq->execute();
    }
}

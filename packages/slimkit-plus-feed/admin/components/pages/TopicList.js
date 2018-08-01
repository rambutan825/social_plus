import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Grid from "@material-ui/core/Grid";
import Paper from '@material-ui/core/Paper';
import Toolbar from '@material-ui/core/Toolbar';
import Table from '@material-ui/core/Table';

const styles = theme => {
  return {
    root: {
      padding: theme.spacing.unit * 3
    },
    paper: {
      width: '100%',
    }
  };
};

class TopicList extends React.Component {
  /**
   * The page prop types check.
   */
  static propTypes = {
    classes: PropTypes.object.isRequired,
  };

  /**
   * The page state.
   */
  static state = {
    topics: []
  };

  render() {
    const { classes } = this.props;

    return (
      <Grid container className={classes.root}>
        <Paper className={classes.paper}>
          <Toolbar>
            <div>2</div>
          </Toolbar>
          <Table>Topic List.</Table>
        </Paper>
      </Grid>
    );
  }

  componentDidMount() {
    console.log(this.props);
  }
}

export default withStyles(styles)(TopicList);
